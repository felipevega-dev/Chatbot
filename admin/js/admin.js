/**
 * JavaScript para el panel de administración
 * AI Chatbot para Uniformes Escolares
 */

(function($) {
    'use strict';

    // Inicialización cuando el DOM está listo
    $(document).ready(function() {
        // Inicializar funcionalidades
        initTabs();
        initColorPicker();
        initPreview();
        initFormHandling();
        initFAQToggle();
        
        // Inicializar características específicas de cada página
        if ($('.ai-chatbot-conversation-container').length) {
            initConversationFeatures();
        }
        
        if ($('.ai-chatbot-analytics-container').length) {
            initAnalyticsFeatures();
        }
        
        if ($('.ai-chatbot-import-data').length) {
            initImportFeatures();
        }
    });

    /**
     * Inicializa las pestañas en el panel de administración
     */
    function initTabs() {
        $('.ai-chatbot-tab-link').on('click', function() {
            var tabId = $(this).data('tab');
            
            // Activar pestaña
            $('.ai-chatbot-tab-link').removeClass('active');
            $(this).addClass('active');
            
            // Mostrar contenido
            $('.ai-chatbot-tab-pane').removeClass('active');
            $('#' + tabId).addClass('active');
            
            // Guardar estado de la pestaña en localStorage
            localStorage.setItem('ai_chatbot_active_tab', tabId);
        });
        
        // Recuperar pestaña guardada
        var activeTab = localStorage.getItem('ai_chatbot_active_tab');
        if (activeTab && $('#' + activeTab).length) {
            $('.ai-chatbot-tab-link[data-tab="' + activeTab + '"]').trigger('click');
        }
    }

    /**
     * Inicializa el selector de color
     */
    function initColorPicker() {
        if ($.fn.wpColorPicker) {
            $('.ai-chatbot-color-picker').wpColorPicker({
                change: function(event, ui) {
                    // Actualizar vista previa al cambiar color
                    updatePreview();
                }
            });
        }
    }

    /**
     * Inicializa la vista previa del chatbot
     */
    function initPreview() {
        // Actualizar vista previa con cambios en inputs
        $('#ai_chatbot_title').on('input', updatePreview);
        $('#ai_chatbot_welcome_message').on('input', updatePreview);
        
        // Actualizar vista previa inicialmente
        updatePreview();
    }

    /**
     * Actualiza la vista previa del chatbot
     */
    function updatePreview() {
        var title = $('#ai_chatbot_title').val();
        var welcomeMessage = $('#ai_chatbot_welcome_message').val();
        var primaryColor = $('.ai-chatbot-color-picker').val() || '#3f51b5';
        
        // Actualizar texto
        $('.ai-chatbot-preview-header span').text(title);
        $('.ai-chatbot-preview-bot span').text(welcomeMessage);
        
        // Actualizar colores
        $('.ai-chatbot-preview-header, .ai-chatbot-preview-user').css('background-color', primaryColor);
    }

    /**
     * Inicializa manejo de formularios
     */
    function initFormHandling() {
        // Confirmación para reiniciar configuración
        $('.ai-chatbot-reset-settings').on('click', function(e) {
            if (!confirm(aiChatbotAdmin.strings.confirm_reset)) {
                e.preventDefault();
                return false;
            }
        });
        
        // Copiar shortcode al portapapeles
        $('.ai-chatbot-copy-shortcode').on('click', function() {
            var shortcode = $(this).data('shortcode');
            copyToClipboard(shortcode);
            
            // Mostrar mensaje de éxito
            var $button = $(this);
            var originalText = $button.text();
            
            $button.text(aiChatbotAdmin.strings.copied);
            
            setTimeout(function() {
                $button.text(originalText);
            }, 2000);
        });
        
        // Evento de prueba de API
        $('.ai-chatbot-test-api').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var originalText = $button.text();
            var apiKey = $('#ai_chatbot_api_key').val();
            
            if (!apiKey) {
                alert(aiChatbotAdmin.strings.no_api_key);
                return;
            }
            
            // Deshabilitar botón y mostrar estado
            $button.prop('disabled', true).text(aiChatbotAdmin.strings.testing);
            
            // Realizar prueba AJAX
            $.ajax({
                url: aiChatbotAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'ai_chatbot_test_api',
                    nonce: aiChatbotAdmin.nonce,
                    api_key: apiKey
                },
                success: function(response) {
                    if (response.success) {
                        $button.removeClass('button-primary').addClass('button-secondary').text(aiChatbotAdmin.strings.test_success);
                        
                        setTimeout(function() {
                            $button.removeClass('button-secondary').addClass('button-primary').text(originalText).prop('disabled', false);
                        }, 3000);
                    } else {
                        $button.text(aiChatbotAdmin.strings.test_error);
                        
                        setTimeout(function() {
                            $button.text(originalText).prop('disabled', false);
                        }, 3000);
                        
                        // Mostrar error
                        alert(response.data.message || aiChatbotAdmin.strings.unknown_error);
                    }
                },
                error: function() {
                    $button.text(aiChatbotAdmin.strings.test_error);
                    
                    setTimeout(function() {
                        $button.text(originalText).prop('disabled', false);
                    }, 3000);
                    
                    // Mostrar error
                    alert(aiChatbotAdmin.strings.ajax_error);
                }
            });
        });
        
        // Cambiar modo del editor para respuestas personalizadas
        $('.ai-chatbot-editor-mode').on('change', function() {
            var mode = $(this).val();
            var $editor = $('#ai_chatbot_custom_responses');
            
            if (mode === 'visual') {
                // Intentar activar editor visual si está disponible
                if (typeof wp !== 'undefined' && wp.editor) {
                    wp.editor.initialize('ai_chatbot_custom_responses', {
                        tinymce: {
                            wpautop: true,
                            plugins: 'charmap colorpicker hr lists paste tabfocus textcolor wordpress wpautoresize wpeditimage wpemoji wpgallery wplink wptextpattern',
                            toolbar1: 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,fullscreen,wp_adv',
                            toolbar2: 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help'
                        },
                        quicktags: true,
                        mediaButtons: false
                    });
                } else {
                    alert(aiChatbotAdmin.strings.visual_editor_unavailable);
                    $(this).val('code').trigger('change');
                }
            } else {
                // Desactivar editor visual si está activo
                if (typeof wp !== 'undefined' && wp.editor) {
                    wp.editor.remove('ai_chatbot_custom_responses');
                }
                
                // Mostrar textarea plano
                $editor.show();
            }
        });
        
        // Actualizar vista de WooCommerce settings cuando se cambia la integración
        $('#ai_chatbot_woocommerce_integration').on('change', function() {
            var enabled = $(this).val() === 'yes';
            $('.ai-chatbot-woo-settings').toggle(enabled);
        }).trigger('change');
    }

    /**
     * Inicializa toggle de preguntas frecuentes
     */
    function initFAQToggle() {
        $('.ai-chatbot-faq-item h4').on('click', function() {
            var $item = $(this).parent();
            var $answer = $item.find('.ai-chatbot-faq-answer');
            
            if ($item.hasClass('active')) {
                $item.removeClass('active');
                $answer.slideUp();
            } else {
                $('.ai-chatbot-faq-item').removeClass('active');
                $('.ai-chatbot-faq-answer').slideUp();
                
                $item.addClass('active');
                $answer.slideDown();
            }
        });
        
        // Abrir la primera pregunta por defecto
        if ($('.ai-chatbot-faq-item').length && !$('.ai-chatbot-faq-item.active').length) {
            $('.ai-chatbot-faq-item:first-child h4').trigger('click');
        }
    }

    /**
     * Copia texto al portapapeles
     *
     * @param {string} text Texto a copiar
     */
    function copyToClipboard(text) {
        // Intentar usar la API moderna de Clipboard
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).catch(function() {
                // Fallback a método antiguo si falla
                copyToClipboardFallback(text);
            });
        } else {
            // Método antiguo para navegadores sin soporte de API
            copyToClipboardFallback(text);
        }
    }
    
    /**
     * Método alternativo para copiar al portapapeles
     *
     * @param {string} text Texto a copiar
     */
    function copyToClipboardFallback(text) {
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(text).select();
        document.execCommand('copy');
        $temp.remove();
    }

    /**
     * Inicializa características para visualización de conversaciones
     */
    function initConversationFeatures() {
        // Botón para exportar conversación
        $('.ai-chatbot-export-conversation').on('click', function(e) {
            e.preventDefault();
            
            var conversationId = $(this).data('id');
            var format = $('.ai-chatbot-export-format').val();
            
            window.location.href = aiChatbotAdmin.ajax_url + '?action=ai_chatbot_export_conversation&id=' + conversationId + '&format=' + format + '&nonce=' + aiChatbotAdmin.nonce;
        });
        
        // Botón para eliminar conversación
        $('.ai-chatbot-delete-conversation').on('click', function(e) {
            e.preventDefault();
            
            if (!confirm(aiChatbotAdmin.strings.confirm_delete_conversation)) {
                return;
            }
            
            var conversationId = $(this).data('id');
            
            $.ajax({
                url: aiChatbotAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'ai_chatbot_delete_conversation',
                    nonce: aiChatbotAdmin.nonce,
                    id: conversationId
                },
                success: function(response) {
                    if (response.success) {
                        // Redirigir a la lista de conversaciones
                        window.location.href = aiChatbotAdmin.conversations_url;
                    } else {
                        alert(response.data.message || aiChatbotAdmin.strings.unknown_error);
                    }
                },
                error: function() {
                    alert(aiChatbotAdmin.strings.ajax_error);
                }
            });
        });
        
        // Filtro de conversaciones
        $('#ai-chatbot-filter-conversations').on('submit', function(e) {
            // No hacer nada especial, el formulario se envía normalmente
        });
        
        // Calificación de respuestas (para conversaciones individuales)
        $('.ai-chatbot-rate-response').on('click', function() {
            var messageId = $(this).data('message-id');
            var rating = $(this).data('rating');
            var $container = $(this).closest('.ai-chatbot-message-rating');
            
            $.ajax({
                url: aiChatbotAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'ai_chatbot_rate_message',
                    nonce: aiChatbotAdmin.nonce,
                    message_id: messageId,
                    rating: rating
                },
                success: function(response) {
                    if (response.success) {
                        // Actualizar UI
                        $container.html('<span class="ai-chatbot-rating-success">' + aiChatbotAdmin.strings.rating_saved + '</span>');
                    } else {
                        alert(response.data.message || aiChatbotAdmin.strings.unknown_error);
                    }
                },
                error: function() {
                    alert(aiChatbotAdmin.strings.ajax_error);
                }
            });
        });
    }

    /**
     * Inicializa características para analíticas
     */
    function initAnalyticsFeatures() {
        // Confirmación para exportar datos
        $('.ai-chatbot-export-data').on('click', function(e) {
            if (!confirm(aiChatbotAdmin.strings.confirm_export)) {
                e.preventDefault();
                return false;
            }
        });
        
        // Selector de rango de fechas
        if ($.fn.datepicker) {
            $('.ai-chatbot-date-range').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                maxDate: 0 // No permitir fechas futuras
            });
        }
        
        // Selector de período rápido
        $('.ai-chatbot-quick-period').on('change', function() {
            var period = $(this).val();
            
            if (period === 'custom') {
                $('.ai-chatbot-custom-date-range').show();
            } else {
                $('.ai-chatbot-custom-date-range').hide();
                $('#ai-chatbot-filter-analytics').submit();
            }
        });
        
        // Resaltar palabras clave en nubes de palabras
        $('.ai-chatbot-word-cloud-item').on('mouseenter', function() {
            $(this).addClass('ai-chatbot-word-highlight');
        }).on('mouseleave', function() {
            $(this).removeClass('ai-chatbot-word-highlight');
        });
    }
    
    /**
     * Inicializa características para importación de datos
     */
    function initImportFeatures() {
        // Vista previa de archivo a importar
        $('#ai_chatbot_import_file').on('change', function() {
            var file = this.files[0];
            var $previewContainer = $('.ai-chatbot-import-preview');
            
            if (!file) {
                $previewContainer.empty();
                return;
            }
            
            // Verificar tipo de archivo
            var allowedTypes = ['application/json', 'text/csv'];
            if (allowedTypes.indexOf(file.type) === -1) {
                alert(aiChatbotAdmin.strings.invalid_file_type);
                this.value = '';
                $previewContainer.empty();
                return;
            }
            
            // Mostrar información básica
            $previewContainer.html(
                '<div class="ai-chatbot-import-file-info">' +
                '<strong>' + aiChatbotAdmin.strings.file_name + ':</strong> ' + file.name + '<br>' +
                '<strong>' + aiChatbotAdmin.strings.file_size + ':</strong> ' + formatFileSize(file.size) + '<br>' +
                '<strong>' + aiChatbotAdmin.strings.file_type + ':</strong> ' + file.type +
                '</div>'
            );
            
            // Si es JSON, intentar mostrar vista previa
            if (file.type === 'application/json') {
                var reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        var json = JSON.parse(e.target.result);
                        var count = Object.keys(json).length;
                        
                        $previewContainer.append(
                            '<div class="ai-chatbot-import-file-preview">' +
                            '<strong>' + aiChatbotAdmin.strings.items_count + ':</strong> ' + count +
                            '</div>'
                        );
                    } catch (error) {
                        $previewContainer.append(
                            '<div class="ai-chatbot-import-file-error">' +
                            aiChatbotAdmin.strings.json_parse_error + ': ' + error.message +
                            '</div>'
                        );
                    }
                };
                reader.readAsText(file);
            }
            
            // Habilitar botón de importación
            $('.ai-chatbot-import-submit').prop('disabled', false);
        });
        
        // Confirmación antes de importar
        $('.ai-chatbot-import-form').on('submit', function(e) {
            if (!confirm(aiChatbotAdmin.strings.confirm_import)) {
                e.preventDefault();
                return false;
            }
            
            // Mostrar indicador de carga
            $('.ai-chatbot-import-progress').show();
        });
    }
    
    /**
     * Formatea tamaño de archivo en unidades legibles
     *
     * @param {number} bytes Tamaño en bytes
     * @return {string} Tamaño formateado
     */
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    /**
     * Procesar dropdowns dinámicos para selección de categorías WooCommerce
     */
    $('.ai-chatbot-product-category-toggle').on('click', function() {
        var $container = $(this).closest('.ai-chatbot-product-categories');
        $container.toggleClass('expanded');
        
        if ($container.hasClass('expanded')) {
            $(this).find('.dashicons').removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-down-alt2');
        } else {
            $(this).find('.dashicons').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-right-alt2');
        }
    });
    
    /**
     * Gestión de preguntas y respuestas personalizadas
     */
    if ($('.ai-chatbot-qa-builder').length) {
        initQABuilder();
    }
    
    /**
     * Inicializa el constructor de preguntas y respuestas
     */
    function initQABuilder() {
        // Añadir nueva pregunta/respuesta
        $('.ai-chatbot-add-qa').on('click', function() {
            var $template = $('.ai-chatbot-qa-template').clone();
            $template.removeClass('ai-chatbot-qa-template').show();
            
            // Generar ID único para este par
            var uniqueId = 'qa-' + Date.now();
            $template.find('input, textarea').each(function() {
                var name = $(this).attr('name');
                $(this).attr('name', name.replace('__id__', uniqueId));
            });
            
            $('.ai-chatbot-qa-items').append($template);
        });
        
        // Eliminar pregunta/respuesta
        $(document).on('click', '.ai-chatbot-remove-qa', function() {
            $(this).closest('.ai-chatbot-qa-item').remove();
        });
        
        // Mover hacia arriba/abajo
        $(document).on('click', '.ai-chatbot-move-qa-up', function() {
            var $item = $(this).closest('.ai-chatbot-qa-item');
            var $prev = $item.prev('.ai-chatbot-qa-item');
            
            if ($prev.length) {
                $item.insertBefore($prev);
            }
        });
        
        $(document).on('click', '.ai-chatbot-move-qa-down', function() {
            var $item = $(this).closest('.ai-chatbot-qa-item');
            var $next = $item.next('.ai-chatbot-qa-item');
            
            if ($next.length) {
                $item.insertAfter($next);
            }
        });
    }

})(jQuery);