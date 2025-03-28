<?php
/**
 * Plantilla para la página de documentación
 *
 * @package AIChatbot
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html__('AI Chatbot para Uniformes - Documentación', 'ai-chatbot-uniformes'); ?></h1>
    
    <div class="ai-chatbot-documentation-container">
        <!-- Introducción -->
        <div class="ai-chatbot-documentation-section">
            <h2><?php echo esc_html__('Introducción', 'ai-chatbot-uniformes'); ?></h2>
            <p><?php echo esc_html__('Bienvenido a la documentación de AI Chatbot para Uniformes Escolares. Este plugin te permite integrar un chatbot inteligente en tu tienda de WordPress para responder consultas de tus clientes sobre uniformes escolares y otros productos.', 'ai-chatbot-uniformes'); ?></p>
            <p><?php echo esc_html__('El chatbot utiliza inteligencia artificial para proporcionar respuestas relevantes y precisas, mejorando la experiencia de usuario y reduciendo la carga de trabajo del equipo de atención al cliente.', 'ai-chatbot-uniformes'); ?></p>
        </div>
        
        <!-- Comenzando -->
        <div class="ai-chatbot-documentation-section">
            <h2><?php echo esc_html__('Comenzando', 'ai-chatbot-uniformes'); ?></h2>
            <p><?php echo esc_html__('Para comenzar a utilizar el chatbot, sigue estos pasos básicos:', 'ai-chatbot-uniformes'); ?></p>
            <ol>
                <li><?php echo esc_html__('Configura una API key en la página de Configuración.', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Personaliza el título y mensaje de bienvenida del chatbot.', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Activa el chatbot en el sitio utilizando la opción "Mostrar en el Footer" o insertando el shortcode.', 'ai-chatbot-uniformes'); ?></li>
            </ol>
            <p><?php echo esc_html__('El chatbot ya está listo para responder consultas básicas de tus clientes. Para obtener respuestas más avanzadas y personalizadas, considera actualizar a un plan de pago.', 'ai-chatbot-uniformes'); ?></p>
        </div>
        
        <!-- Uso del Shortcode -->
        <div class="ai-chatbot-documentation-section">
            <h2><?php echo esc_html__('Uso del Shortcode', 'ai-chatbot-uniformes'); ?></h2>
            <p><?php echo esc_html__('Puedes insertar el chatbot en cualquier página o publicación utilizando el siguiente shortcode:', 'ai-chatbot-uniformes'); ?></p>
            
            <div class="ai-chatbot-documentation-shortcode">
                [ai_chatbot]
            </div>
            
            <p><?php echo esc_html__('Este shortcode mostrará el chatbot en línea dentro del contenido. También puedes personalizar el shortcode con los siguientes atributos:', 'ai-chatbot-uniformes'); ?></p>
            
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Atributo', 'ai-chatbot-uniformes'); ?></th>
                        <th><?php echo esc_html__('Descripción', 'ai-chatbot-uniformes'); ?></th>
                        <th><?php echo esc_html__('Valor por defecto', 'ai-chatbot-uniformes'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>title</code></td>
                        <td><?php echo esc_html__('Título del chatbot', 'ai-chatbot-uniformes'); ?></td>
                        <td><?php echo esc_html(get_option('ai_chatbot_title', __('Asistente de Uniformes', 'ai-chatbot-uniformes'))); ?></td>
                    </tr>
                    <tr>
                        <td><code>welcome_message</code></td>
                        <td><?php echo esc_html__('Mensaje de bienvenida', 'ai-chatbot-uniformes'); ?></td>
                        <td><?php echo esc_html(get_option('ai_chatbot_welcome_message', __('¡Hola! 👋 Soy el asistente virtual de Uniformes Escolares. ¿En qué puedo ayudarte hoy?', 'ai-chatbot-uniformes'))); ?></td>
                    </tr>
                    <tr>
                        <td><code>position</code></td>
                        <td><?php echo esc_html__('Posición del chatbot (inline o fixed)', 'ai-chatbot-uniformes'); ?></td>
                        <td>inline</td>
                    </tr>
                </tbody>
            </table>
            
            <p><?php echo esc_html__('Ejemplo de uso con atributos:', 'ai-chatbot-uniformes'); ?></p>
            
            <div class="ai-chatbot-documentation-shortcode">
                [ai_chatbot title="Asistente de Ventas" welcome_message="¡Bienvenido a nuestra tienda! ¿En qué puedo ayudarte?" position="inline"]
            </div>
        </div>
        
        <!-- Integración con WooCommerce -->
        <div class="ai-chatbot-documentation-section">
            <h2><?php echo esc_html__('Integración con WooCommerce', 'ai-chatbot-uniformes'); ?></h2>
            <p><?php echo esc_html__('El chatbot puede integrarse con WooCommerce para proporcionar información sobre productos, stock, precios y más. Esta característica está disponible en los planes Basic y Premium.', 'ai-chatbot-uniformes'); ?></p>
            
            <h3><?php echo esc_html__('Configuración', 'ai-chatbot-uniformes'); ?></h3>
            <p><?php echo esc_html__('Para habilitar la integración con WooCommerce:', 'ai-chatbot-uniformes'); ?></p>
            <ol>
                <li><?php echo esc_html__('Ve a la pestaña "Avanzado" en la configuración del chatbot', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Activa la opción "Integración con WooCommerce"', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Selecciona las categorías de productos que deseas incluir', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Guarda los cambios', 'ai-chatbot-uniformes'); ?></li>
            </ol>
            
            <h3><?php echo esc_html__('Funciones disponibles', 'ai-chatbot-uniformes'); ?></h3>
            <p><?php echo esc_html__('Con la integración de WooCommerce, el chatbot puede:', 'ai-chatbot-uniformes'); ?></p>
            <ul>
                <li><?php echo esc_html__('Proporcionar información sobre productos específicos', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Verificar disponibilidad de stock', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Informar sobre precios y descuentos', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Sugerir productos basados en consultas del usuario', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Guiar al usuario a través del proceso de compra', 'ai-chatbot-uniformes'); ?></li>
            </ul>
        </div>
        
        <!-- APIs y Personalización -->
        <div class="ai-chatbot-documentation-section">
            <h2><?php echo esc_html__('APIs y Personalización', 'ai-chatbot-uniformes'); ?></h2>
            
            <h3><?php echo esc_html__('Configuración de API', 'ai-chatbot-uniformes'); ?></h3>
            <p><?php echo esc_html__('El chatbot utiliza la API de OpenAI para generar respuestas. Necesitas configurar una API key en la página de Configuración para habilitar las funciones avanzadas.', 'ai-chatbot-uniformes'); ?></p>
            <p><?php echo esc_html__('Puedes obtener una API key en: ', 'ai-chatbot-uniformes'); ?><a href="https://platform.openai.com/api-keys" target="_blank">https://platform.openai.com/api-keys</a></p>
            
            <h3><?php echo esc_html__('Personalización avanzada', 'ai-chatbot-uniformes'); ?></h3>
            <p><?php echo esc_html__('Los usuarios con plan Premium pueden acceder a opciones de personalización avanzada, como:', 'ai-chatbot-uniformes'); ?></p>
            <ul>
                <li><?php echo esc_html__('Instrucciones personalizadas para la IA', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Bloqueo de palabras/temas', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Entrenamiento con datos propios', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Personalización completa del diseño', 'ai-chatbot-uniformes'); ?></li>
            </ul>
        </div>
        
        <!-- Preguntas y Respuestas Personalizadas -->
        <div class="ai-chatbot-documentation-section">
            <h2><?php echo esc_html__('Preguntas y Respuestas Personalizadas', 'ai-chatbot-uniformes'); ?></h2>
            <p><?php echo esc_html__('Puedes configurar respuestas personalizadas para preguntas específicas, lo que es útil para:', 'ai-chatbot-uniformes'); ?></p>
            <ul>
                <li><?php echo esc_html__('Responder preguntas frecuentes de manera consistente', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Proporcionar información específica sobre tu negocio', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Asegurar que ciertas preguntas tengan respuestas aprobadas manualmente', 'ai-chatbot-uniformes'); ?></li>
            </ul>
            
            <h3><?php echo esc_html__('Cómo configurar respuestas personalizadas', 'ai-chatbot-uniformes'); ?></h3>
            <ol>
                <li><?php echo esc_html__('Ve a la pestaña "Avanzado" en la configuración', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Desplázate hasta la sección "Respuestas Personalizadas"', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Haz clic en "Añadir respuesta personalizada"', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Ingresa una o más preguntas (variaciones) y la respuesta deseada', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Guarda los cambios', 'ai-chatbot-uniformes'); ?></li>
            </ol>
            
            <p><?php echo esc_html__('El chatbot priorizará estas respuestas personalizadas sobre las generadas por la IA cuando detecte preguntas similares.', 'ai-chatbot-uniformes'); ?></p>
        </div>
        
        <!-- Analíticas y Reportes -->
        <div class="ai-chatbot-documentation-section">
            <h2><?php echo esc_html__('Analíticas y Reportes', 'ai-chatbot-uniformes'); ?></h2>
            <p><?php echo esc_html__('El plugin incluye una sección de analíticas que te permite monitorear el rendimiento del chatbot y obtener insights sobre las interacciones de los usuarios.', 'ai-chatbot-uniformes'); ?></p>
            
            <h3><?php echo esc_html__('Analíticas básicas (todos los planes)', 'ai-chatbot-uniformes'); ?></h3>
            <ul>
                <li><?php echo esc_html__('Número total de conversaciones', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Número total de mensajes', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Promedio de mensajes por conversación', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Consultas más frecuentes', 'ai-chatbot-uniformes'); ?></li>
            </ul>
            
            <h3><?php echo esc_html__('Analíticas avanzadas (Plan Premium)', 'ai-chatbot-uniformes'); ?></h3>
            <ul>
                <li><?php echo esc_html__('Tasa de satisfacción del usuario', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Tiempo promedio de respuesta', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Análisis de temas populares', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Historial detallado de conversaciones', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Exportación de datos en múltiples formatos', 'ai-chatbot-uniformes'); ?></li>
            </ul>
        </div>
        
        <!-- Solución de Problemas -->
        <div class="ai-chatbot-documentation-section">
            <h2><?php echo esc_html__('Solución de Problemas', 'ai-chatbot-uniformes'); ?></h2>
            
            <h3><?php echo esc_html__('Problemas comunes', 'ai-chatbot-uniformes'); ?></h3>
            <div class="ai-chatbot-faq-item">
                <h4><?php echo esc_html__('El chatbot no aparece en el sitio', 'ai-chatbot-uniformes'); ?></h4>
                <div class="ai-chatbot-faq-answer">
                    <p><?php echo esc_html__('Verifica los siguientes puntos:', 'ai-chatbot-uniformes'); ?></p>
                    <ul>
                        <li><?php echo esc_html__('Asegúrate de que la opción "Mostrar en el Footer" está activada en la configuración.', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Comprueba si hay conflictos con otros plugins o temas utilizando herramientas de desarrollo del navegador.', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Verifica que no hay errores JavaScript en la consola del navegador.', 'ai-chatbot-uniformes'); ?></li>
                    </ul>
                </div>
            </div>
            
            <div class="ai-chatbot-faq-item">
                <h4><?php echo esc_html__('El chatbot no genera respuestas inteligentes', 'ai-chatbot-uniformes'); ?></h4>
                <div class="ai-chatbot-faq-answer">
                    <p><?php echo esc_html__('Si el chatbot solo devuelve respuestas básicas predefinidas:', 'ai-chatbot-uniformes'); ?></p>
                    <ul>
                        <li><?php echo esc_html__('Verifica que has configurado correctamente la API key de OpenAI.', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Asegúrate de que tu plan actual tiene suficientes consultas disponibles.', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Comprueba si hay errores en el registro de WordPress relacionados con la API.', 'ai-chatbot-uniformes'); ?></li>
                    </ul>
                </div>
            </div>
            
            <div class="ai-chatbot-faq-item">
                <h4><?php echo esc_html__('Problemas de rendimiento', 'ai-chatbot-uniformes'); ?></h4>
                <div class="ai-chatbot-faq-answer">
                    <p><?php echo esc_html__('Si el chatbot responde con lentitud:', 'ai-chatbot-uniformes'); ?></p>
                    <ul>
                        <li><?php echo esc_html__('Verifica la velocidad de tu servidor y optimiza WordPress.', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Considera utilizar un plugin de caché compatible.', 'ai-chatbot-uniformes'); ?></li>
                        <li><?php echo esc_html__('Reduce el tamaño del contexto en la configuración avanzada (usuarios Premium).', 'ai-chatbot-uniformes'); ?></li>
                    </ul>
                </div>
            </div>
            
            <h3><?php echo esc_html__('Soporte técnico', 'ai-chatbot-uniformes'); ?></h3>
            <p><?php echo esc_html__('Si necesitas ayuda adicional, puedes contactarnos a través de los siguientes canales:', 'ai-chatbot-uniformes'); ?></p>
            <ul>
                <li><?php echo esc_html__('Email: soporte@aichatbot-uniformes.com', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Foro de soporte: https://aichatbot-uniformes.com/soporte', 'ai-chatbot-uniformes'); ?></li>
                <li><?php echo esc_html__('Documentación completa: https://aichatbot-uniformes.com/docs', 'ai-chatbot-uniformes'); ?></li>
            </ul>
            <p><?php echo esc_html__('Los usuarios con planes de pago tienen acceso a soporte prioritario.', 'ai-chatbot-uniformes'); ?></p>
        </div>
        
        <!-- Hooks para desarrolladores -->
        <div class="ai-chatbot-documentation-section">
            <h2><?php echo esc_html__('Hooks para Desarrolladores', 'ai-chatbot-uniformes'); ?></h2>
            <p><?php echo esc_html__('El plugin proporciona varios filtros y acciones que los desarrolladores pueden utilizar para personalizar el comportamiento del chatbot.', 'ai-chatbot-uniformes'); ?></p>
            
            <h3><?php echo esc_html__('Filtros disponibles', 'ai-chatbot-uniformes'); ?></h3>
            <div class="ai-chatbot-doc-code-block">
                <p><code>ai_chatbot_system_prompt</code> - <?php echo esc_html__('Modificar el prompt del sistema enviado a la IA', 'ai-chatbot-uniformes'); ?></p>
                <pre><code>add_filter('ai_chatbot_system_prompt', function($prompt, $user_plan) {
    // Personaliza el prompt según tus necesidades
    return $prompt . " Además, sé amigable y usa emojis.";
}, 10, 2);</code></pre>
                
                <p><code>ai_chatbot_user_message</code> - <?php echo esc_html__('Modificar el mensaje del usuario antes de enviarlo a la IA', 'ai-chatbot-uniformes'); ?></p>
                <pre><code>add_filter('ai_chatbot_user_message', function($message) {
    // Procesa o limpia el mensaje del usuario
    return $message;
});</code></pre>
                
                <p><code>ai_chatbot_response</code> - <?php echo esc_html__('Modificar la respuesta generada antes de enviarla al usuario', 'ai-chatbot-uniformes'); ?></p>
                <pre><code>add_filter('ai_chatbot_response', function($response, $message) {
    // Personaliza la respuesta
    return $response;
}, 10, 2);</code></pre>
                
                <p><code>ai_chatbot_appearance</code> - <?php echo esc_html__('Modificar la configuración de apariencia del chatbot', 'ai-chatbot-uniformes'); ?></p>
                <pre><code>add_filter('ai_chatbot_appearance', function($appearance) {
    // Personaliza la apariencia
    $appearance['primary_color'] = '#FF5722';
    return $appearance;
});</code></pre>
            </div>
            
            <h3><?php echo esc_html__('Acciones disponibles', 'ai-chatbot-uniformes'); ?></h3>
            <div class="ai-chatbot-doc-code-block">
                <p><code>ai_chatbot_before_process_message</code> - <?php echo esc_html__('Se ejecuta antes de procesar un mensaje', 'ai-chatbot-uniformes'); ?></p>
                <pre><code>add_action('ai_chatbot_before_process_message', function($message, $session_id) {
    // Ejecuta acciones antes de procesar el mensaje
}, 10, 2);</code></pre>
                
                <p><code>ai_chatbot_after_process_message</code> - <?php echo esc_html__('Se ejecuta después de procesar un mensaje', 'ai-chatbot-uniformes'); ?></p>
                <pre><code>add_action('ai_chatbot_after_process_message', function($message, $response, $session_id) {
    // Ejecuta acciones después de procesar el mensaje
}, 10, 3);</code></pre>
                
                <p><code>ai_chatbot_conversation_saved</code> - <?php echo esc_html__('Se ejecuta después de guardar una conversación en la base de datos', 'ai-chatbot-uniformes'); ?></p>
                <pre><code>add_action('ai_chatbot_conversation_saved', function($conversation_id, $user_message, $bot_response) {
    // Ejecuta acciones después de guardar la conversación
}, 10, 3);</code></pre>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Manejo de preguntas y respuestas
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
    });
</script>

<style>
    .ai-chatbot-doc-code-block pre {
        background-color: #f6f8fa;
        padding: 15px;
        border-radius: 5px;
        overflow-x: auto;
        margin-bottom: 20px;
    }
    
    .ai-chatbot-faq-item h4 {
        cursor: pointer;
        padding: 10px 0;
        margin: 0;
        color: #2271b1;
    }
    
    .ai-chatbot-faq-item h4:hover {
        color: #135e96;
    }
    
    .ai-chatbot-faq-item h4:before {
        content: "▶";
        display: inline-block;
        margin-right: 10px;
        font-size: 10px;
        color: #999;
        transition: transform 0.3s ease;
    }
    
    .ai-chatbot-faq-item.active h4:before {
        content: "▼";
    }
    
    .ai-chatbot-faq-answer {
        display: none;
        padding: 0 0 15px 20px;
    }
</style>