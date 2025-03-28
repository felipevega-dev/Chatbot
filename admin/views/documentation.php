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
            <p><?