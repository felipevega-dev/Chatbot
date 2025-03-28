/**
 * JavaScript para la funcionalidad del chatbot
 * AI Chatbot para Uniformes Escolares
 */

(function($) {
    'use strict';

    // Variables globales
    let chatOpen = false;
    let isProcessing = false;
    const sessionId = generateSessionId();
    let conversationHistory = [];

    // Inicialización cuando el DOM está listo
    $(document).ready(function() {
        initChatbot();
        setupEventListeners();
    });

    /**
     * Inicializa el chatbot
     */
    function initChatbot() {
        // Verificar si el chatbot ya está abierto en sesión anterior
        if (localStorage.getItem('ai_chatbot_open') === 'true') {
            toggleChat(true);
        }

        // Establecer cookie de sesión
        setCookie('ai_chatbot_session', sessionId, 30);
    }

    /**
     * Configura los listeners de eventos
     */
    function setupEventListeners() {
        // Evento para abrir/cerrar el chatbot
        $('#ai-chatbot-toggle, .ai-chatbot-minimize').on('click', function() {
            toggleChat();
        });

        // Evento para enviar mensaje
        $('.ai-chatbot-send').on('click', function() {
            sendMessage();
        });

        // Evento para enviar mensaje con Enter
        $('.ai-chatbot-input textarea').on('keydown', function(e) {
            if (e.keyCode === 13 && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Auto-ajustar altura del textarea
        $('.ai-chatbot-input textarea').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (Math.min(this.scrollHeight, 100)) + 'px';
        });

        // Evento personalizado para añadir mensajes programáticamente
        $(document).on('ai_chatbot_add_message', function(e, data) {
            if (data.type === 'bot') {
                addBotMessage(data.message);
            } else {
                addUserMessage(data.message);
            }
        });
    }

    /**
     * Muestra/oculta el chatbot
     *
     * @param {boolean} forceOpen Forzar apertura
     */
    function toggleChat(forceOpen = null) {
        // Si se proporciona un valor, usarlo; de lo contrario, invertir el estado actual
        chatOpen = (forceOpen !== null) ? forceOpen : !chatOpen;
        
        if (chatOpen) {
            $('.ai-chatbot-container').removeClass('ai-chatbot-closed').addClass('ai-chatbot-open');
            $('#ai-chatbot-toggle').addClass('ai-chatbot-toggle-active');
            localStorage.setItem('ai_chatbot_open', 'true');
        } else {
            $('.ai-chatbot-container').removeClass('ai-chatbot-open').addClass('ai-chatbot-closed');
            $('#ai-chatbot-toggle').removeClass('ai-chatbot-toggle-active');
            localStorage.setItem('ai_chatbot_open', 'false');
        }
    }

    /**
     * Envía un mensaje al servidor
     */
    function sendMessage() {
        // Obtener mensaje del usuario
        const userInput = $('.ai-chatbot-input textarea').val().trim();
        
        // Verificar si hay texto y no está procesando
        if (userInput === '' || isProcessing) {
            return;
        }
        
        // Deshabilitar la entrada durante el procesamiento
        isProcessing = true;
        $('.ai-chatbot-send').prop('disabled', true);
        
        // Limpiar input
        $('.ai-chatbot-input textarea').val('');
        $('.ai-chatbot-input textarea').css('height', 'auto');
        
        // Mostrar mensaje del usuario
        addUserMessage(userInput);
        
        // Mostrar indicador de escritura
        addTypingIndicator();
        
        // Enviar mensaje al servidor
        $.ajax({
            url: aiChatbotData.ajax_url,
            type: 'POST',
            data: {
                action: 'ai_chatbot_message',
                nonce: aiChatbotData.nonce,
                message: userInput,
                session_id: sessionId
            },
            success: function(response) {
                // Quitar indicador de escritura
                removeTypingIndicator();
                
                // Habilitar entrada nuevamente
                isProcessing = false;
                $('.ai-chatbot-send').prop('disabled', false);
                
                if (response.success) {
                    // Mostrar respuesta del bot
                    addBotMessage(response.data.message);
                    
                    // Actualizar contador de consultas
                    if (response.data.remaining !== undefined) {
                        $('.ai-chatbot-queries').text(response.data.remaining + ' consultas restantes');
                    }
                } else {
                    // Mensaje de error
                    let errorMessage = response.data.message || aiChatbotData.strings.error_message;
                    addBotMessage(errorMessage);
                    
                    // Si es necesario actualizar
                    if (response.data.upgrade) {
                        setTimeout(function() {
                            addBotMessage('<a href="' + response.data.upgrade_link + '" class="ai-chatbot-upgrade">' + aiChatbotData.strings.upgrade_message + '</a>');
                        }, 500);
                    }
                }
            },
            error: function() {
                // Quitar indicador de escritura
                removeTypingIndicator();
                
                // Habilitar entrada nuevamente
                isProcessing = false;
                $('.ai-chatbot-send').prop('disabled', false);
                
                // Mensaje de error
                addBotMessage(aiChatbotData.strings.error_message);
            }
        });
    }

    /**
     * Añade un mensaje del usuario
     *
     * @param {string} message Mensaje
     */
    function addUserMessage(message) {
        const messageHtml = `
            <div class="ai-chatbot-message ai-chatbot-user-message">
                <div class="ai-chatbot-message-content">${escapeHtml(message)}</div>
            </div>
        `;
        
        $('.ai-chatbot-messages').append(messageHtml);
        scrollToBottom();
        
        // Guardar en historial
        conversationHistory.push({
            role: 'user',
            content: message
        });
    }

    /**
     * Añade un mensaje del bot
     *
     * @param {string} message Mensaje
     */
    function addBotMessage(message) {
        const messageHtml = `
            <div class="ai-chatbot-message ai-chatbot-bot-message">
                <div class="ai-chatbot-avatar">
                    <img src="${aiChatbotData.plugin_url}assets/images/bot-avatar.png" alt="Bot" onerror="this.onerror=null;this.src='data:image/svg+xml;charset=UTF-8,<svg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'32\\' height=\\'32\\' viewBox=\\'0 0 24 24\\'><path fill=\\'%23666\\' d=\\'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z\\'></path></svg>'">
                </div>
                <div class="ai-chatbot-message-content">${formatBotMessage(message)}</div>
            </div>
        `;
        
        $('.ai-chatbot-messages').append(messageHtml);
        scrollToBottom();
        
        // Guardar en historial
        conversationHistory.push({
            role: 'assistant',
            content: message
        });
    }

    /**
     * Añade indicador de escritura
     */
    function addTypingIndicator() {
        const typingHtml = `
            <div class="ai-chatbot-message ai-chatbot-bot-message ai-chatbot-typing">
                <div class="ai-chatbot-avatar">
                    <img src="${aiChatbotData.plugin_url}assets/images/bot-avatar.png" alt="Bot" onerror="this.onerror=null;this.src='data:image/svg+xml;charset=UTF-8,<svg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'32\\' height=\\'32\\' viewBox=\\'0 0 24 24\\'><path fill=\\'%23666\\' d=\\'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z\\'></path></svg>'">
                </div>
                <div class="ai-chatbot-message-content">
                    <div class="ai-chatbot-typing-indicator">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        
        $('.ai-chatbot-messages').append(typingHtml);
        scrollToBottom();
    }

    /**
     * Quita indicador de escritura
     */
    function removeTypingIndicator() {
        $('.ai-chatbot-typing').remove();
    }

    /**
     * Hace scroll hasta el final
     */
    function scrollToBottom() {
        const messagesContainer = $('.ai-chatbot-messages');
        messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
    }

    /**
     * Formatea mensaje del bot (convierte enlaces, etc.)
     *
     * @param {string} message Mensaje del bot
     * @return {string} Mensaje formateado
     */
    function formatBotMessage(message) {
        // Convertir URLs en enlaces clicables
        message = message.replace(
            /(https?:\/\/[^\s]+)/g, 
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
        );
        
        // Convertir saltos de línea en <br>
        message = message.replace(/\n/g, '<br>');
        
        return message;
    }

    /**
     * Escapa HTML para prevenir XSS
     *
     * @param {string} text Texto a escapar
     * @return {string} Texto escapado
     */
    function escapeHtml(text) {
        return $('<div/>').text(text).html();
    }

    /**
     * Genera ID de sesión
     *
     * @return {string} ID de sesión
     */
    function generateSessionId() {
        // Usar sessionId existente si está en cookie
        const existingSession = getCookie('ai_chatbot_session');
        if (existingSession) {
            return existingSession;
        }
        
        // Generar nuevo UUID v4
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    /**
     * Establece una cookie
     *
     * @param {string} name Nombre de la cookie
     * @param {string} value Valor de la cookie
     * @param {number} days Días de expiración
     */
    function setCookie(name, value, days) {
        let expires = '';
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + (value || '') + expires + '; path=/';
    }

    /**
     * Obtiene una cookie
     *
     * @param {string} name Nombre de la cookie
     * @return {string|null} Valor de la cookie o null
     */
    function getCookie(name) {
        const nameEQ = name + '=';
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }

})(jQuery);