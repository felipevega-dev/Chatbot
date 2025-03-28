<?php
/**
 * Clase para manejar la integración con APIs de IA
 *
 * @package AIChatbot
 */

namespace AIChatbot;

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase que maneja las integraciones con APIs de IA
 */
class Chatbot_API {

    /**
     * API key para el servicio de IA
     *
     * @var string
     */
    private $api_key;

    /**
     * Modelo de IA a utilizar
     *
     * @var string
     */
    private $model;

    /**
     * Inicializa la clase API
     */
    public function init() {
        $this->api_key = get_option('ai_chatbot_api_key', '');
        $this->model = get_option('ai_chatbot_model', 'gpt-3.5-turbo');
    }

    /**
     * Obtiene respuesta de la IA
     *
     * @param string $message Mensaje del usuario
     * @param array $history Historial de conversación
     * @param string $user_plan Plan del usuario (basic o premium)
     * @return string Respuesta de la IA
     */
    public function get_ai_response($message, $history, $user_plan) {
        // Verificar si la API key está configurada
        if (empty($this->api_key)) {
            return __('El chatbot no está correctamente configurado. Por favor contacta al administrador.', 'ai-chatbot-uniformes');
        }

        // Seleccionar modelo según el plan
        $model = ($user_plan === 'premium') ? 'gpt-4' : 'gpt-3.5-turbo';
        
        // Construir el contexto para el modelo
        $system_message = $this->get_system_prompt();
        
        // Preparar mensajes para la API
        $messages = [
            ['role' => 'system', 'content' => $system_message]
        ];
        
        // Añadir historial si existe
        if (!empty($history)) {
            $messages = array_merge($messages, $history);
        }
        
        // Añadir mensaje actual
        $messages[] = ['role' => 'user', 'content' => $message];
        
        // Llamar a la API de OpenAI
        $response = $this->call_openai_api($model, $messages);
        
        // Verificar si hubo error
        if (is_wp_error($response)) {
            error_log('Error en API OpenAI: ' . $response->get_error_message());
            return $this->get_fallback_response();
        }
        
        // Extraer respuesta
        $content = $this->extract_response_content($response);
        
        if (empty($content)) {
            return $this->get_fallback_response();
        }
        
        return $content;
    }
    
    /**
     * Construye el prompt del sistema con el contexto específico
     *
     * @return string Prompt del sistema
     */
    private function get_system_prompt() {
        $prompt = __("Eres un asistente virtual especializado en la venta de uniformes escolares. ", 'ai-chatbot-uniformes');
        $prompt .= __("Tu objetivo es ayudar a los clientes a encontrar los uniformes adecuados, responder preguntas sobre tallas, precios, disponibilidad, y políticas de la tienda. ", 'ai-chatbot-uniformes');
        $prompt .= __("Eres amable, paciente y siempre dispuesto a ayudar. ", 'ai-chatbot-uniformes');
        
        // Añadir información específica sobre productos si están disponibles
        $catalog_data = $this->get_product_catalog_data();
        if (!empty($catalog_data)) {
            $prompt .= __("Aquí tienes información sobre nuestro catálogo actual: ", 'ai-chatbot-uniformes');
            $prompt .= wp_json_encode($catalog_data);
        }
        
        // Añadir información sobre políticas específicas
        $prompt .= __("\n\nPolíticas de la tienda:\n", 'ai-chatbot-uniformes');
        $prompt .= __("- Devoluciones: Aceptamos devoluciones dentro de los 30 días posteriores a la compra con el comprobante de pago.\n", 'ai-chatbot-uniformes');
        $prompt .= __("- Cambios: Los cambios de talla son gratuitos siempre que las prendas no hayan sido utilizadas.\n", 'ai-chatbot-uniformes');
        $prompt .= __("- Formas de pago: Aceptamos efectivo, tarjetas de crédito/débito y transferencias bancarias.\n", 'ai-chatbot-uniformes');
        $prompt .= __("- Envíos: Realizamos envíos a domicilio con costo adicional dependiendo de la zona.\n", 'ai-chatbot-uniformes');
        
        return $prompt;
    }
    
    /**
     * Llama a la API de OpenAI para obtener una respuesta
     *
     * @param string $model Modelo a utilizar
     * @param array $messages Mensajes para la API
     * @return array|WP_Error Respuesta de la API o error
     */
    private function call_openai_api($model, $messages) {
        $url = 'https://api.openai.com/v1/chat/completions';
        
        $body = [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => 500,
            'temperature' => 0.7,
        ];
        
        $args = [
            'body' => wp_json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key
            ],
            'timeout' => 30
        ];
        
        $response = wp_remote_post($url, $args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (wp_remote_retrieve_response_code($response) !== 200) {
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : 'Error desconocido en la API';
            return new \WP_Error('api_error', $error_message);
        }
        
        return $data;
    }
    
    /**
     * Extrae el contenido de la respuesta de la API
     *
     * @param array $response Respuesta de la API
     * @return string Contenido de la respuesta
     */
    private function extract_response_content($response) {
        if (isset($response['choices'][0]['message']['content'])) {
            return $response['choices'][0]['message']['content'];
        }
        
        return '';
    }
    
    /**
     * Obtiene una respuesta de fallback en caso de error
     *
     * @return string Respuesta de fallback
     */
    private function get_fallback_response() {
        $fallback_responses = [
            __('Lo siento, estoy experimentando problemas para procesar tu solicitud. ¿Podrías intentarlo de nuevo?', 'ai-chatbot-uniformes'),
            __('Parece que hay un problema técnico. Por favor, intenta de nuevo en unos momentos.', 'ai-chatbot-uniformes'),
            __('Disculpa la molestia, no puedo responder en este momento. Si es urgente, te recomiendo contactar directamente con la tienda.', 'ai-chatbot-uniformes')
        ];
        
        return $fallback_responses[array_rand($fallback_responses)];
    }
    
    /**
     * Obtiene datos del catálogo de productos
     *
     * @return array Datos del catálogo
     */
    private function get_product_catalog_data() {
        // Verificar si WooCommerce está activo
        if (!function_exists('wc_get_products')) {
            return [];
        }
        
        $products = wc_get_products([
            'limit' => 100,
            'category' => ['uniformes-escolares']
        ]);
        
        $catalog = [];
        
        foreach ($products as $product) {
            $catalog[] = [
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'description' => $product->get_short_description(),
                'stock' => $product->get_stock_quantity(),
                'attributes' => $this->get_product_attributes($product)
            ];
        }
        
        return $catalog;
    }
    
    /**
     * Obtiene los atributos de un producto
     *
     * @param WC_Product $product Producto
     * @return array Atributos del producto
     */
    private function get_product_attributes($product) {
        $attributes = [];
        
        foreach ($product->get_attributes() as $attribute) {
            if ($attribute->is_taxonomy()) {
                $attribute_taxonomy = $attribute->get_taxonomy_object();
                $attribute_values = wc_get_product_terms($product->get_id(), $attribute->get_name(), ['fields' => 'names']);
                $attributes[$attribute_taxonomy->attribute_label] = $attribute_values;
            } else {
                $attributes[$attribute->get_name()] = $attribute->get_options();
            }
        }
        
        return $attributes;
    }
}