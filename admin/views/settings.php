<?php
/**
 * Plantilla para la página de configuración del plugin
 *
 * @package AIChatbot
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html__('AI Chatbot para Uniformes - Configuración', 'ai-chatbot-uniformes'); ?></h1>
    
    <?php settings_errors(); ?>
    
    <div class="ai-chatbot-admin-container">
        <div class="ai-chatbot-admin-sidebar">
            <div class="ai-chatbot-admin-box ai-chatbot-plan-info">
                <h3><?php echo esc_html__('Plan Actual', 'ai-chatbot-uniformes'); ?></h3>
                <?php 
                $plan = $this->licensing->get_user_plan(); 
                $plan_names = [
                    'free' => __('Gratuito', 'ai-chatbot-uniformes'),
                    'basic' => __('Básico', 'ai-chatbot-uniformes'),
                    'premium' => __('Premium', 'ai-chatbot-uniformes')
                ];
                $plan_name = isset($plan_names[$plan]) ? $plan_names[$plan] : ucfirst($plan);
                ?>
                <div class="ai-chatbot-plan-badge ai-chatbot-plan-<?php echo esc_attr($plan); ?>">
                    <?php echo esc_html($plan_name); ?>
                </div>
                
                <?php if ($plan !== 'premium'): ?>
                    <p><?php echo esc_html__('Consultas restantes:', 'ai-chatbot-uniformes'); ?> 
                        <strong><?php echo esc_html($this->licensing->get_remaining_queries()); ?></strong>
                    </p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=ai-chatbot-licensing')); ?>" class="button button-primary">
                        <?php echo esc_html__('Actualizar a Premium', 'ai-chatbot-uniformes'); ?>
                    </a>
                <?php else: ?>
                    <p><?php echo esc_html__('Disfrutas de consultas ilimitadas y todas las funciones premium.', 'ai-chatbot-uniformes'); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="ai-chatbot-admin-box">
                <h3><?php echo esc_html__('Soporte', 'ai-chatbot-uniformes'); ?></h3>
                <p><?php echo esc_html__('¿Necesitas ayuda con la configuración?', 'ai-chatbot-uniformes'); ?></p>
                <ul>
                    <li><a href="#" target="_blank"><?php echo esc_html__('Documentación', 'ai-chatbot-uniformes'); ?></a></li>
                    <li><a href="#" target="_blank"><?php echo esc_html__('Contactar Soporte', 'ai-chatbot-uniformes'); ?></a></li>
                </ul>
            </div>
        </div>
        
        <div class="ai-chatbot-admin-content">
            <div class="ai-chatbot-admin-tabs">
                <button class="ai-chatbot-tab-link active" data-tab="general"><?php echo esc_html__('General', 'ai-chatbot-uniformes'); ?></button>
                <button class="ai-chatbot-tab-link" data-tab="appearance"><?php echo esc_html__('Apariencia', 'ai-chatbot-uniformes'); ?></button>
                <button class="ai-chatbot-tab-link" data-tab="advanced"><?php echo esc_html__('Avanzado', 'ai-chatbot-uniformes'); ?></button>
            </div>
            
            <div class="ai-chatbot-tab-content">
                <!-- Pestaña General -->
                <div id="general" class="ai-chatbot-tab-pane active">
                    <form method="post" action="options.php">
                        <?php settings_fields('ai_chatbot_general'); ?>
                        
                        <div class="ai-chatbot-form-section">
                            <h3><?php echo esc_html__('Configuración de API', 'ai-chatbot-uniformes'); ?></h3>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="ai_chatbot_api_key"><?php echo esc_html__('API Key', 'ai-chatbot-uniformes'); ?></label>
                                    </th>
                                    <td>
                                        <input type="password" id="ai_chatbot_api_key" name="ai_chatbot_api_key" class="regular-text" 
                                               value="<?php echo esc_attr(get_option('ai_chatbot_api_key', '')); ?>" />
                                        <p class="description">
                                            <?php echo esc_html__('Introduce tu API Key de OpenAI. Puedes obtenerla en https://platform.openai.com/api-keys', 'ai-chatbot-uniformes'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="ai_chatbot_model"><?php echo esc_html__('Modelo de IA', 'ai-chatbot-uniformes'); ?></label>
                                    </th>
                                    <td>
                                        <select id="ai_chatbot_model" name="ai_chatbot_model" class="regular-text">
                                            <?php
                                            $current_model = get_option('ai_chatbot_model', 'gpt-3.5-turbo');
                                            $models = [
                                                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
                                                'gpt-4' => 'GPT-4 (Premium)',
                                                'gpt-4o' => 'GPT-4o (Premium)',
                                            ];
                                            
                                            foreach ($models as $model_id => $model_name) {
                                                printf(
                                                    '<option value="%s" %s>%s</option>',
                                                    esc_attr($model_id),
                                                    selected($current_model, $model_id, false),
                                                    esc_html($model_name)
                                                );
                                            }
                                            ?>
                                        </select>
                                        <p class="description">
                                            <?php echo esc_html__('Selecciona el modelo de IA a utilizar. Los modelos más avanzados requieren plan Premium.', 'ai-chatbot-uniformes'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="ai-chatbot-form-section">
                            <h3><?php echo esc_html__('Visualización', 'ai-chatbot-uniformes'); ?></h3>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="ai_chatbot_show_in_footer"><?php echo esc_html__('Mostrar en el Footer', 'ai-chatbot-uniformes'); ?></label>
                                    </th>
                                    <td>
                                        <select id="ai_chatbot_show_in_footer" name="ai_chatbot_show_in_footer">
                                            <option value="yes" <?php selected(get_option('ai_chatbot_show_in_footer', 'yes'), 'yes'); ?>>
                                                <?php echo esc_html__('Sí - Mostrar botón flotante', 'ai-chatbot-uniformes'); ?>
                                            </option>
                                            <option value="no" <?php selected(get_option('ai_chatbot_show_in_footer', 'yes'), 'no'); ?>>
                                                <?php echo esc_html__('No - Solo mediante shortcode', 'ai-chatbot-uniformes'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php echo esc_html__('Selecciona si quieres mostrar el chatbot flotante en todas las páginas.', 'ai-chatbot-uniformes'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="ai_chatbot_avoid_duplicate"><?php echo esc_html__('Evitar duplicación', 'ai-chatbot-uniformes'); ?></label>
                                    </th>
                                    <td>
                                        <select id="ai_chatbot_avoid_duplicate" name="ai_chatbot_avoid_duplicate">
                                            <option value="yes" <?php selected(get_option('ai_chatbot_avoid_duplicate', 'yes'), 'yes'); ?>>
                                                <?php echo esc_html__('Sí', 'ai-chatbot-uniformes'); ?>
                                            </option>
                                            <option value="no" <?php selected(get_option('ai_chatbot_avoid_duplicate', 'yes'), 'no'); ?>>
                                                <?php echo esc_html__('No', 'ai-chatbot-uniformes'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php echo esc_html__('Si seleccionas "Sí", el botón flotante no se mostrará en páginas donde ya exista el shortcode.', 'ai-chatbot-uniformes'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <?php submit_button(); ?>
                    </form>
                </div>
                
                <!-- Pestaña Apariencia -->
                <div id="appearance" class="ai-chatbot-tab-pane">
                    <form method="post" action="options.php">
                        <?php settings_fields('ai_chatbot_appearance'); ?>
                        
                        <div class="ai-chatbot-form-section">
                            <h3><?php echo esc_html__('Personalización', 'ai-chatbot-uniformes'); ?></h3>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="ai_chatbot_title"><?php echo esc_html__('Título del Chatbot', 'ai-chatbot-uniformes'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="ai_chatbot_title" name="ai_chatbot_title" class="regular-text" 
                                               value="<?php echo esc_attr(get_option('ai_chatbot_title', __('Asistente de Uniformes', 'ai-chatbot-uniformes'))); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="ai_chatbot_welcome_message"><?php echo esc_html__('Mensaje de Bienvenida', 'ai-chatbot-uniformes'); ?></label>
                                    </th>
                                    <td>
                                        <textarea id="ai_chatbot_welcome_message" name="ai_chatbot_welcome_message" class="large-text" rows="3"><?php echo esc_textarea(get_option('ai_chatbot_welcome_message', __('¡Hola! 👋 Soy el asistente virtual de Uniformes Escolares. ¿En qué puedo ayudarte hoy?', 'ai-chatbot-uniformes'))); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="ai_chatbot_primary_color"><?php echo esc_html__('Color Principal', 'ai-chatbot-uniformes'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="ai_chatbot_primary_color" name="ai_chatbot_primary_color" class="ai-chatbot-color-picker" 
                                               value="<?php echo esc_attr(get_option('ai_chatbot_primary_color', '#3f51b5')); ?>" />
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="ai-chatbot-form-section">
                            <h3><?php echo esc_html__('Vista Previa', 'ai-chatbot-uniformes'); ?></h3>
                            
                            <div class="ai-chatbot-preview">
                                <div class="ai-chatbot-preview-container">
                                    <div class="ai-chatbot-preview-header">
                                        <span id="preview-title"><?php echo esc_html(get_option('ai_chatbot_title', __('Asistente de Uniformes', 'ai-chatbot-uniformes'))); ?></span>
                                    </div>
                                    <div class="ai-chatbot-preview-messages">
                                        <div class="ai-chatbot-preview-message ai-chatbot-preview-bot">
                                            <span id="preview-welcome"><?php echo esc_html(get_option('ai_chatbot_welcome_message', __('¡Hola! 👋 Soy el asistente virtual de Uniformes Escolares. ¿En qué puedo ayudarte hoy?', 'ai-chatbot-uniformes'))); ?></span>
                                        </div>
                                        <div class="ai-chatbot-preview-message ai-chatbot-preview-user">
                                            <span><?php echo esc_html__('¿Tienen uniformes para primaria?', 'ai-chatbot-uniformes'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php submit_button(); ?>
                    </form>
                </div>
                
                <!-- Pestaña Avanzado -->
                <div id="advanced" class="ai-chatbot-tab-pane">
                    <form method="post" action="options.php">
                        <?php
                        // Verificar si el usuario tiene plan premium
                        $has_premium = ($this->licensing->get_user_plan() === 'premium');
                        
                        if (!$has_premium):
                        ?>
                            <div class="ai-chatbot-premium-notice">
                                <h3><?php echo esc_html__('Funciones Avanzadas', 'ai-chatbot-uniformes'); ?></h3>
                                <p><?php echo esc_html__('Las opciones de configuración avanzada están disponibles exclusivamente para usuarios con Plan Premium.', 'ai-chatbot-uniformes'); ?></p>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=ai-chatbot-licensing')); ?>" class="button button-primary">
                                    <?php echo esc_html__('Actualizar a Premium', 'ai-chatbot-uniformes'); ?>
                                </a>
                            </div>
                            
                            <div class="ai-chatbot-premium-features">
                                <h4><?php echo esc_html__('Funciones Premium', 'ai-chatbot-uniformes'); ?></h4>
                                <ul>
                                    <li><?php echo esc_html__('Personalización avanzada del comportamiento del chatbot', 'ai-chatbot-uniformes'); ?></li>
                                    <li><?php echo esc_html__('Integración con catálogo de WooCommerce', 'ai-chatbot-uniformes'); ?></li>
                                    <li><?php echo esc_html__('Entrenamiento personalizado con datos propios', 'ai-chatbot-uniformes'); ?></li>
                                    <li><?php echo esc_html__('Bloqueo de palabras/temas específicos', 'ai-chatbot-uniformes'); ?></li>
                                    <li><?php echo esc_html__('Exportación de conversaciones', 'ai-chatbot-uniformes'); ?></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <?php settings_fields('ai_chatbot_advanced'); ?>
                            
                            <div class="ai-chatbot-form-section">
                                <h3><?php echo esc_html__('Configuración Avanzada', 'ai-chatbot-uniformes'); ?></h3>
                                
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">
                                            <label for="ai_chatbot_context_size"><?php echo esc_html__('Tamaño de Contexto', 'ai-chatbot-uniformes'); ?></label>
                                        </th>
                                        <td>
                                            <select id="ai_chatbot_context_size" name="ai_chatbot_context_size">
                                                <option value="5" <?php selected(get_option('ai_chatbot_context_size', '10'), '5'); ?>>5</option>
                                                <option value="10" <?php selected(get_option('ai_chatbot_context_size', '10'), '10'); ?>>10</option>
                                                <option value="15" <?php selected(get_option('ai_chatbot_context_size', '10'), '15'); ?>>15</option>
                                                <option value="20" <?php selected(get_option('ai_chatbot_context_size', '10'), '20'); ?>>20</option>
                                            </select>
                                            <p class="description">
                                                <?php echo esc_html__('Número de mensajes anteriores que se envían como contexto a la IA.', 'ai-chatbot-uniformes'); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="ai_chatbot_woocommerce_integration"><?php echo esc_html__('Integración con WooCommerce', 'ai-chatbot-uniformes'); ?></label>
                                        </th>
                                        <td>
                                            <select id="ai_chatbot_woocommerce_integration" name="ai_chatbot_woocommerce_integration">
                                                <option value="yes" <?php selected(get_option('ai_chatbot_woocommerce_integration', 'yes'), 'yes'); ?>>
                                                    <?php echo esc_html__('Activado', 'ai-chatbot-uniformes'); ?>
                                                </option>
                                                <option value="no" <?php selected(get_option('ai_chatbot_woocommerce_integration', 'yes'), 'no'); ?>>
                                                    <?php echo esc_html__('Desactivado', 'ai-chatbot-uniformes'); ?>
                                                </option>
                                            </select>
                                            <p class="description">
                                                <?php echo esc_html__('Permite al chatbot acceder a información de productos y categorías de WooCommerce.', 'ai-chatbot-uniformes'); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="ai_chatbot_blocked_words"><?php echo esc_html__('Palabras bloqueadas', 'ai-chatbot-uniformes'); ?></label>
                                        </th>
                                        <td>
                                            <textarea id="ai_chatbot_blocked_words" name="ai_chatbot_blocked_words" class="large-text" rows="3"><?php echo esc_textarea(get_option('ai_chatbot_blocked_words', '')); ?></textarea>
                                            <p class="description">
                                                <?php echo esc_html__('Lista de palabras separadas por comas que serán bloqueadas en las conversaciones.', 'ai-chatbot-uniformes'); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="ai_chatbot_custom_instructions"><?php echo esc_html__('Instrucciones personalizadas', 'ai-chatbot-uniformes'); ?></label>
                                        </th>
                                        <td>
                                            <textarea id="ai_chatbot_custom_instructions" name="ai_chatbot_custom_instructions" class="large-text" rows="5"><?php echo esc_textarea(get_option('ai_chatbot_custom_instructions', '')); ?></textarea>
                                            <p class="description">
                                                <?php echo esc_html__('Instrucciones adicionales para personalizar el comportamiento del chatbot.', 'ai-chatbot-uniformes'); ?>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <?php submit_button(); ?>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Javascript para las tabs y para la vista previa
    jQuery(document).ready(function($) {
        // Manejo de pestañas
        $('.ai-chatbot-tab-link').on('click', function() {
            var tabId = $(this).data('tab');
            
            // Activar pestaña
            $('.ai-chatbot-tab-link').removeClass('active');
            $(this).addClass('active');
            
            // Mostrar contenido
            $('.ai-chatbot-tab-pane').removeClass('active');
            $('#' + tabId).addClass('active');
        });
        
        // Inicializar color picker
        if ($.fn.wpColorPicker) {
            $('.ai-chatbot-color-picker').wpColorPicker({
                change: function(event, ui) {
                    updatePreview();
                }
            });
        }
        
        // Actualizar vista previa en tiempo real
        $('#ai_chatbot_title').on('input', updatePreview);
        $('#ai_chatbot_welcome_message').on('input', updatePreview);
        
        function updatePreview() {
            var title = $('#ai_chatbot_title').val();
            var welcomeMessage = $('#ai_chatbot_welcome_message').val();
            var primaryColor = $('#ai_chatbot_primary_color').val();
            
            $('#preview-title').text(title);
            $('#preview-welcome').text(welcomeMessage);
            
            $('.ai-chatbot-preview-header, .ai-chatbot-preview-user').css('background-color', primaryColor);
        }
    });
</script>

<style>
    /* Estilos para la interfaz de administración */
    .ai-chatbot-admin-container {
        display: flex;
        margin-top: 20px;
        gap: 20px;
    }
    
    .ai-chatbot-admin-sidebar {
        width: 25%;
        min-width: 250px;
    }
    
    .ai-chatbot-admin-content {
        flex: 1;
    }
    
    .ai-chatbot-admin-box {
        background: #fff;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        margin-bottom: 20px;
        padding: 15px;
    }
    
    .ai-chatbot-plan-info {
        background-color: #f8f9fa;
    }
    
    .ai-chatbot-plan-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 3px;
        color: #fff;
        font-weight: bold;
        margin: 10px 0;
    }
    
    .ai-chatbot-plan-free {
        background-color: #6c757d;
    }
    
    .ai-chatbot-plan-basic {
        background-color: #28a745;
    }
    
    .ai-chatbot-plan-premium {
        background-color: #007bff;
    }
    
    .ai-chatbot-admin-tabs {
        margin-bottom: 20px;
        border-bottom: 1px solid #ccc;
    }
    
    .ai-chatbot-tab-link {
        background: none;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        font-size: 14px;
        margin-right: 5px;
        border-bottom: 2px solid transparent;
    }
    
    .ai-chatbot-tab-link.active {
        border-bottom: 2px solid #2271b1;
        font-weight: bold;
        color: #2271b1;
    }
    
    .ai-chatbot-tab-pane {
        display: none;
        background: #fff;
        padding: 20px;
        border: 1px solid #ccd0d4;
    }
    
    .ai-chatbot-tab-pane.active {
        display: block;
    }
    
    .ai-chatbot-form-section {
        margin-bottom: 30px;
    }
    
    .ai-chatbot-premium-notice {
        background-color: #f0f6fc;
        border-left: 4px solid #2271b1;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .ai-chatbot-premium-features {
        background-color: #f8f9fa;
        padding: 15px;
        border: 1px solid #e2e4e7;
    }
    
    .ai-chatbot-premium-features ul {
        list-style-type: disc;
        margin-left: 20px;
    }
    
    /* Estilos para la vista previa */
    .ai-chatbot-preview {
        background-color: #f8f9fa;
        padding: 20px;
        border: 1px solid #e2e4e7;
    }
    
    .ai-chatbot-preview-container {
        width: 300px;
        height: 300px;
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .ai-chatbot-preview-header {
        background-color: #3f51b5;
        color: white;
        padding: 10px;
        font-weight: bold;
    }
    
    .ai-chatbot-preview-messages {
        flex: 1;
        background-color: #f9f9f9;
        padding: 10px;
        overflow-y: auto;
    }
    
    .ai-chatbot-preview-message {
        margin-bottom: 10px;
        max-width: 80%;
        padding: 8px 12px;
        border-radius: 15px;
    }
    
    .ai-chatbot-preview-bot {
        background-color: #e9e9e9;
        align-self: flex-start;
    }
    
    .ai-chatbot-preview-user {
        background-color: #3f51b5;
        color: white;
        margin-left: auto;
        align-self: flex-end;
    }
    
    /* Responsive */
    @media (max-width: 782px) {
        .ai-chatbot-admin-container {
            flex-direction: column;
        }
        
        .ai-chatbot-admin-sidebar {
            width: 100%;
        }
    }
</style>