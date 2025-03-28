<?php
/**
 * Plantilla para mostrar el chatbot en el frontend
 *
 * @package AIChatbot
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Obtener datos de la configuración o atributos
$title = isset($atts['title']) ? $atts['title'] : __('Asistente de Uniformes', 'ai-chatbot-uniformes');
$welcome_message = isset($atts['welcome_message']) ? $atts['welcome_message'] : __('¡Hola! 👋 Soy el asistente virtual de Uniformes Escolares. ¿En qué puedo ayudarte hoy?', 'ai-chatbot-uniformes');
$position = isset($atts['position']) ? $atts['position'] : 'fixed';
$class = isset($class) ? $class : '';
?>

<?php if ($position === 'fixed'): ?>
<!-- Botón flotante para abrir el chatbot -->
<button id="ai-chatbot-toggle" class="ai-chatbot-toggle">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ai-chatbot-icon-chat">
        <path d="M21 11.5C21.0034 12.8199 20.6951 14.1219 20.1 15.3C19.3944 16.7118 18.3098 17.8992 16.9674 18.7293C15.6251 19.5594 14.0782 19.9994 12.5 20C11.1801 20.0035 9.87812 19.6951 8.7 19.1L3 21L4.9 15.3C4.30493 14.1219 3.99656 12.8199 4 11.5C4.00061 9.92179 4.44061 8.37488 5.27072 7.03258C6.10083 5.69028 7.28825 4.6056 8.7 3.90003C9.87812 3.30496 11.1801 2.99659 12.5 3.00003H13C15.0843 3.11502 17.053 3.99479 18.5291 5.47089C20.0052 6.94699 20.885 8.91568 21 11V11.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ai-chatbot-icon-close">
        <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>
<?php endif; ?>

<!-- Contenedor principal del chatbot -->
<div id="ai-chatbot-container" class="ai-chatbot-container <?php echo esc_attr($class); ?> <?php echo ($position === 'fixed') ? 'ai-chatbot-fixed ai-chatbot-closed' : 'ai-chatbot-inline'; ?>">
    
    <!-- Encabezado del chatbot -->
    <div class="ai-chatbot-header">
        <div class="ai-chatbot-title"><?php echo esc_html($title); ?></div>
        <?php if ($position === 'fixed'): ?>
        <div class="ai-chatbot-controls">
            <button class="ai-chatbot-minimize">−</button>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Área de mensajes -->
    <div class="ai-chatbot-messages">
        <!-- Los mensajes se cargarán dinámicamente con JavaScript -->
    </div>
    
    <!-- Área de entrada -->
    <div class="ai-chatbot-input">
        <textarea placeholder="<?php echo esc_attr__('Escribe tu pregunta aquí...', 'ai-chatbot-uniformes'); ?>"></textarea>
        <button class="ai-chatbot-send">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
    
    <!-- Footer del chatbot -->
    <div class="ai-chatbot-footer">
        <div class="ai-chatbot-powered"><?php echo esc_html__('Powered by AI Chatbot', 'ai-chatbot-uniformes'); ?></div>
        <div class="ai-chatbot-status">
            <span class="ai-chatbot-plan"><?php echo esc_html(ucfirst($this->licensing->get_user_plan())); ?></span>
            <?php if ($this->licensing->get_user_plan() !== 'premium'): ?>
            <span class="ai-chatbot-queries"><?php echo esc_html($this->licensing->get_remaining_queries()); ?> <?php echo esc_html__('consultas restantes', 'ai-chatbot-uniformes'); ?></span>
            <?php endif; ?>
        </div>
    </div>
    
</div>

<script type="text/javascript">
    // Mensaje de bienvenida para mostrar automáticamente al cargar
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            // Verificar si la variable aiChatbotData está definida (scripts cargados)
            if (typeof aiChatbotData !== 'undefined' && typeof jQuery !== 'undefined') {
                if (jQuery('.ai-chatbot-messages').length > 0 && jQuery('.ai-chatbot-messages').children().length === 0) {
                    // Añadir mensaje de bienvenida
                    var welcomeMessage = "<?php echo esc_js($welcome_message); ?>";
                    jQuery(document).trigger('ai_chatbot_add_message', {
                        message: welcomeMessage,
                        type: 'bot'
                    });
                }
            }
        }, 1000);
    });
</script>