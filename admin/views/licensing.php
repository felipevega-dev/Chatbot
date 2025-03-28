<?php
/**
 * Plantilla para la página de licencias y suscripciones
 *
 * @package AIChatbot
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Obtener el plan actual
$current_plan = $this->licensing->get_user_plan();
?>

<div class="wrap">
    <h1><?php echo esc_html__('AI Chatbot para Uniformes - Licencias y Planes', 'ai-chatbot-uniformes'); ?></h1>
    
    <?php settings_errors(); ?>
    
    <div class="ai-chatbot-licensing-container">
        <div class="ai-chatbot-licensing-header">
            <h2><?php echo esc_html__('Selecciona el plan que mejor se adapte a tus necesidades', 'ai-chatbot-uniformes'); ?></h2>
            <p><?php echo esc_html__('Mejora la experiencia de tus clientes con nuestro chatbot inteligente especializado en uniformes escolares.', 'ai-chatbot-uniformes'); ?></p>
        </div>
        
        <div class="ai-chatbot-plans">
            <!-- Plan Gratuito -->
            <div class="ai-chatbot-plan <?php echo ($current_plan === 'free') ? 'ai-chatbot-current-plan' : ''; ?>">
                <div class="ai-chatbot-plan-header">
                    <h3><?php echo esc_html__('Gratuito', 'ai-chatbot-uniformes'); ?></h3>
                    <div class="ai-chatbot-plan-price">
                        <span class="ai-chatbot-price"><?php echo esc_html__('$0', 'ai-chatbot-uniformes'); ?></span>
                        <span class="ai-chatbot-period"><?php echo esc_html__('/mes', 'ai-chatbot-uniformes'); ?></span>
                    </div>
                    <?php if ($current_plan === 'free'): ?>
                        <div class="ai-chatbot-plan-badge"><?php echo esc_html__('Plan Actual', 'ai-chatbot-uniformes'); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="ai-chatbot-plan-features">
                    <ul>
                        <li><?php echo esc_html__('Chatbot básico', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('20 consultas mensuales', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Respuestas pre-programadas', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Personalización básica', 'ai-chatbot-uniformes'); ?></li>
                        <li class="ai-chatbot-feature-disabled"><?php echo esc_html__('Integración con IA avanzada', 'ai-chatbot-uniformes'); ?></li>
                        <li class="ai-chatbot-feature-disabled"><?php echo esc_html__('Integración con catálogo', 'ai-chatbot-uniformes'); ?></li>
                        <li class="ai-chatbot-feature-disabled"><?php echo esc_html__('Analíticas avanzadas', 'ai-chatbot-uniformes'); ?></li>
                    </ul>
                </div>
                
                <div class="ai-chatbot-plan-action">
                    <?php if ($current_plan !== 'free'): ?>
                        <button class="button" disabled><?php echo esc_html__('Usar Plan Gratuito', 'ai-chatbot-uniformes'); ?></button>
                    <?php else: ?>
                        <button class="button" disabled><?php echo esc_html__('Plan Actual', 'ai-chatbot-uniformes'); ?></button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Plan Básico -->
            <div class="ai-chatbot-plan <?php echo ($current_plan === 'basic') ? 'ai-chatbot-current-plan' : ''; ?> ai-chatbot-plan-highlighted">
                <div class="ai-chatbot-plan-header">
                    <h3><?php echo esc_html__('Básico', 'ai-chatbot-uniformes'); ?></h3>
                    <div class="ai-chatbot-plan-price">
                        <span class="ai-chatbot-price"><?php echo esc_html__('$19', 'ai-chatbot-uniformes'); ?></span>
                        <span class="ai-chatbot-period"><?php echo esc_html__('/mes', 'ai-chatbot-uniformes'); ?></span>
                    </div>
                    <?php if ($current_plan === 'basic'): ?>
                        <div class="ai-chatbot-plan-badge"><?php echo esc_html__('Plan Actual', 'ai-chatbot-uniformes'); ?></div>
                    <?php else: ?>
                        <div class="ai-chatbot-plan-badge ai-chatbot-plan-popular"><?php echo esc_html__('Más Popular', 'ai-chatbot-uniformes'); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="ai-chatbot-plan-features">
                    <ul>
                        <li><?php echo esc_html__('Chatbot con IA', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('100 consultas mensuales', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Personalización completa', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Modelo GPT-3.5 Turbo', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Integración con catálogo', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Analíticas básicas', 'ai-chatbot-uniformes'); ?></li>
                        <li class="ai-chatbot-feature-disabled"><?php echo esc_html__('Analíticas avanzadas', 'ai-chatbot-uniformes'); ?></li>
                    </ul>
                </div>
                
                <div class="ai-chatbot-plan-action">
                    <?php if ($current_plan !== 'basic'): ?>
                        <?php if (class_exists('WooCommerce')): ?>
                            <a href="<?php echo esc_url(add_query_arg('plan', 'basic', get_permalink(get_option('ai_chatbot_basic_product_id', 0)))); ?>" class="button button-primary">
                                <?php echo esc_html__('Actualizar a Básico', 'ai-chatbot-uniformes'); ?>
                            </a>
                        <?php else: ?>
                            <a href="#" class="button button-primary ai-chatbot-purchase-plan" data-plan="basic">
                                <?php echo esc_html__('Actualizar a Básico', 'ai-chatbot-uniformes'); ?>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="button" disabled><?php echo esc_html__('Plan Actual', 'ai-chatbot-uniformes'); ?></button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Plan Premium -->
            <div class="ai-chatbot-plan <?php echo ($current_plan === 'premium') ? 'ai-chatbot-current-plan' : ''; ?>">
                <div class="ai-chatbot-plan-header">
                    <h3><?php echo esc_html__('Premium', 'ai-chatbot-uniformes'); ?></h3>
                    <div class="ai-chatbot-plan-price">
                        <span class="ai-chatbot-price"><?php echo esc_html__('$49', 'ai-chatbot-uniformes'); ?></span>
                        <span class="ai-chatbot-period"><?php echo esc_html__('/mes', 'ai-chatbot-uniformes'); ?></span>
                    </div>
                    <?php if ($current_plan === 'premium'): ?>
                        <div class="ai-chatbot-plan-badge"><?php echo esc_html__('Plan Actual', 'ai-chatbot-uniformes'); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="ai-chatbot-plan-features">
                    <ul>
                        <li><?php echo esc_html__('Chatbot con IA avanzada', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Consultas ilimitadas', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Personalización avanzada', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Acceso a modelos GPT-4', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Integración avanzada con catálogo', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Analíticas avanzadas', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Entrenamiento con datos propios', 'ai-chatbot-uniformes'); ?></li>
                    </ul>
                </div>
                
                <div class="ai-chatbot-plan-action">
                    <?php if ($current_plan !== 'premium'): ?>
                        <?php if (class_exists('WooCommerce')): ?>
                            <a href="<?php echo esc_url(add_query_arg('plan', 'premium', get_permalink(get_option('ai_chatbot_premium_product_id', 0)))); ?>" class="button button-primary">
                                <?php echo esc_html__('Actualizar a Premium', 'ai-chatbot-uniformes'); ?>
                            </a>
                        <?php else: ?>
                            <a href="#" class="button button-primary ai-chatbot-purchase-plan" data-plan="premium">
                                <?php echo esc_html__('Actualizar a Premium', 'ai-chatbot-uniformes'); ?>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="button" disabled><?php echo esc_html__('Plan Actual', 'ai-chatbot-uniformes'); ?></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="ai-chatbot-licensing-info">
            <div class="ai-chatbot-info-box">
                <h3><?php echo esc_html__('Información de Licencia', 'ai-chatbot-uniformes'); ?></h3>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo esc_html__('Plan Actual', 'ai-chatbot-uniformes'); ?></th>
                        <td>
                            <strong><?php echo esc_html(ucfirst($current_plan)); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__('Consultas Disponibles', 'ai-chatbot-uniformes'); ?></th>
                        <td>
                            <?php if ($current_plan === 'premium'): ?>
                                <strong><?php echo esc_html__('Ilimitadas', 'ai-chatbot-uniformes'); ?></strong>
                            <?php else: ?>
                                <strong><?php echo esc_html($this->licensing->get_remaining_queries()); ?></strong> 
                                <?php echo esc_html__('de', 'ai-chatbot-uniformes'); ?> 
                                <?php 
                                    $limits = ['free' => 20, 'basic' => 100];
                                    echo esc_html($limits[$current_plan] ?? 0); 
                                ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__('Próximo Reinicio', 'ai-chatbot-uniformes'); ?></th>
                        <td>
                            <?php 
                                $next_month = strtotime('+1 month', strtotime(date('Y-m-01')));
                                echo esc_html(date_i18n(get_option('date_format'), $next_month)); 
                            ?>
                        </td>
                    </tr>
                    <?php if (get_option('ai_chatbot_license_key', '')): ?>
                    <tr>
                        <th scope="row"><?php echo esc_html__('Clave de Licencia', 'ai-chatbot-uniformes'); ?></th>
                        <td>
                            <code><?php echo esc_html(substr(get_option('ai_chatbot_license_key'), 0, 6) . '...' . substr(get_option('ai_chatbot_license_key'), -4)); ?></code>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <?php if (get_option('ai_chatbot_license_key', '')): ?>
                <form method="post" action="options.php" class="ai-chatbot-deactivate-form">
                    <?php settings_fields('ai_chatbot_licensing'); ?>
                    <input type="hidden" name="ai_chatbot_deactivate_license" value="1">
                    <p>
                        <button type="submit" class="button ai-chatbot-deactivate-license" onclick="return confirm('<?php echo esc_js(__('¿Estás seguro de que deseas desactivar tu licencia? Esto cancelará tu suscripción actual.', 'ai-chatbot-uniformes')); ?>');">
                            <?php echo esc_html__('Desactivar Licencia', 'ai-chatbot-uniformes'); ?>
                        </button>
                    </p>
                </form>
                <?php endif; ?>
            </div>
            
            <div class="ai-chatbot-info-box">
                <h3><?php echo esc_html__('Activar Licencia Manualmente', 'ai-chatbot-uniformes'); ?></h3>
                <p><?php echo esc_html__('Si ya has adquirido una licencia y necesitas activarla manualmente, introduce tu clave de licencia a continuación:', 'ai-chatbot-uniformes'); ?></p>
                
                <form method="post" action="options.php">
                    <?php settings_fields('ai_chatbot_licensing'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="ai_chatbot_license_key"><?php echo esc_html__('Clave de Licencia', 'ai-chatbot-uniformes'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="ai_chatbot_license_key" name="ai_chatbot_license_key" class="regular-text" value="<?php echo esc_attr(get_option('ai_chatbot_license_key', '')); ?>" placeholder="XXXX-XXXX-XXXX-XXXX">
                            </td>
                        </tr>
                    </table>
                    
                    <?php submit_button(__('Activar Licencia', 'ai-chatbot-uniformes')); ?>
                </form>
            </div>
        </div>
        
        <div class="ai-chatbot-faq">
            <h3><?php echo esc_html__('Preguntas Frecuentes', 'ai-chatbot-uniformes'); ?></h3>
            
            <div class="ai-chatbot-faq-item">
                <h4><?php echo esc_html__('¿Cómo se cuentan las consultas?', 'ai-chatbot-uniformes'); ?></h4>
                <div class="ai-chatbot-faq-answer">
                    <p><?php echo esc_html__('Cada vez que un visitante envía un mensaje al chatbot, se cuenta como una consulta. Las respuestas del bot no cuentan como consultas adicionales.', 'ai-chatbot-uniformes'); ?></p>
                </div>
            </div>
            
            <div class="ai-chatbot-faq-item">
                <h4><?php echo esc_html__('¿Puedo cambiar de plan en cualquier momento?', 'ai-chatbot-uniformes'); ?></h4>
                <div class="ai-chatbot-faq-answer">
                    <p><?php echo esc_html__('Sí, puedes actualizar o cambiar tu plan en cualquier momento. Si actualizas a un plan superior, tendrás acceso inmediato a todas sus características.', 'ai-chatbot-uniformes'); ?></p>
                </div>
            </div>
            
            <div class="ai-chatbot-faq-item">
                <h4><?php echo esc_html__('¿Qué ocurre si supero el límite de consultas?', 'ai-chatbot-uniformes'); ?></h4>
                <div class="ai-chatbot-faq-answer">
                    <p><?php echo esc_html__('Si alcanzas el límite de consultas en el plan gratuito o básico, el chatbot seguirá funcionando pero utilizando respuestas pre-programadas básicas en lugar de la IA avanzada. Puedes actualizar tu plan para aumentar el límite o esperar al próximo ciclo mensual.', 'ai-chatbot-uniformes'); ?></p>
                </div>
            </div>
            
            <div class="ai-chatbot-faq-item">
                <h4><?php echo esc_html__('¿Necesito una API key propia de OpenAI?', 'ai-chatbot-uniformes'); ?></h4>
                <div class="ai-chatbot-faq-answer">
                    <p><?php echo esc_html__('Para los planes básico y premium, puedes usar nuestra API integrada sin necesidad de tener una propia. Sin embargo, para mayor control y personalización, tienes la opción de usar tu propia API key.', 'ai-chatbot-uniformes'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos para la página de licencias */
    .ai-chatbot-licensing-container {
        margin-top: 20px;
    }
    
    .ai-chatbot-licensing-header {
        margin-bottom: 30px;
    }
    
    .ai-chatbot-plans {
        display: flex;
        gap: 20px;
        margin-bottom: 40px;
    }
    
    .ai-chatbot-plan {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        background-color: #fff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .ai-chatbot-plan:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .ai-chatbot-plan-highlighted {
        border: 2px solid #2271b1;
        transform: scale(1.05);
    }
    
    .ai-chatbot-plan-highlighted:hover {
        transform: translateY(-5px) scale(1.05);
    }
    
    .ai-chatbot-current-plan {
        border: 2px solid #46b450;
    }
    
    .ai-chatbot-plan-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        text-align: center;
        position: relative;
    }
    
    .ai-chatbot-plan-price {
        margin: 15px 0;
    }
    
    .ai-chatbot-price {
        font-size: 32px;
        font-weight: bold;
    }
    
    .ai-chatbot-period {
        font-size: 14px;
        color: #666;
    }
    
    .ai-chatbot-plan-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #46b450;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .ai-chatbot-plan-popular {
        background-color: #ff9800;
    }
    
    .ai-chatbot-plan-features {
        padding: 20px;
    }
    
    .ai-chatbot-plan-features ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .ai-chatbot-plan-features li {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        position: relative;
        padding-left: 25px;
    }
    
    .ai-chatbot-plan-features li:before {
        content: "✓";
        color: #46b450;
        position: absolute;
        left: 0;
        font-weight: bold;
    }
    
    .ai-chatbot-feature-disabled {
        color: #999;
    }
    
    .ai-chatbot-feature-disabled:before {
        content: "✕" !important;
        color: #999 !important;
    }
    
    .ai-chatbot-plan-action {
        padding: 20px;
        text-align: center;
    }
    
    .ai-chatbot-plan-action .button {
        width: 100%;
        padding: 8px 12px;
        height: auto;
    }
    
    .ai-chatbot-licensing-info {
        display: flex;
        gap: 20px;
        margin-bottom: 40px;
    }
    
    .ai-chatbot-info-box {
        flex: 1;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
    }
    
    .ai-chatbot-deactivate-license {
        color: #b32d2e;
    }
    
    .ai-chatbot-faq {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .ai-chatbot-faq-item {
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }
    
    .ai-chatbot-faq-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .ai-chatbot-faq-item h4 {
        margin: 0 0 10px 0;
        cursor: pointer;
    }
    
    .ai-chatbot-faq-answer {
        display: none;
    }
    
    .ai-chatbot-faq-item.active .ai-chatbot-faq-answer {
        display: block;
    }
    
    /* Responsive */
    @media (max-width: 782px) {
        .ai-chatbot-plans {
            flex-direction: column;
        }
        
        .ai-chatbot-plan-highlighted {
            transform: none;
            order: -1;
        }
        
        .ai-chatbot-plan-highlighted:hover {
            transform: translateY(-5px);
        }
        
        .ai-chatbot-licensing-info {
            flex-direction: column;
        }
    }
</style>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Mostrar/ocultar respuestas de FAQ al hacer clic
        $('.ai-chatbot-faq-item h4').on('click', function() {
            var item = $(this).parent();
            if (item.hasClass('active')) {
                item.removeClass('active');
                item.find('.ai-chatbot-faq-answer').slideUp();
            } else {
                $('.ai-chatbot-faq-item').removeClass('active');
                $('.ai-chatbot-faq-answer').slideUp();
                item.addClass('active');
                item.find('.ai-chatbot-faq-answer').slideDown();
            }
        });
        
        // Modal de compra para cuando no está WooCommerce
        $('.ai-chatbot-purchase-plan').on('click', function(e) {
            e.preventDefault();
            var plan = $(this).data('plan');
            
            // Aquí podrías mostrar un modal o redirigir a una página externa de pago
            alert('Para adquirir el plan ' + plan + ', por favor ponte en contacto con nosotros o instala WooCommerce para gestionar los pagos automáticamente.');
        });
    });
</script>