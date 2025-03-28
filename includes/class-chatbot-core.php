<?php
/**
 * Clase principal del Chatbot
 *
 * @package AIChatbot
 */

namespace AIChatbot;

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase principal que maneja la funcionalidad del chatbot
 */
class Chatbot_Core {

    /**
     * Instancia única de la clase (Singleton)
     *
     * @var Chatbot_Core
     */
    private static $instance = null;

    /**
     * Instancia de la clase API
     *
     * @var Chatbot_API
     */
    private $api;

    /**
     * Instancia de la clase Licensing
     *
     * @var Chatbot_Licensing
     */
    private $licensing;

    /**
     * Constructor
     */
    public function __construct() {
        // Este constructor está vacío intencionalmente ya que la inicialización
        // ocurre en el método init() para mantener una carga más controlada
    }

    /**
     * Inicializa el plugin y carga las dependencias
     */
    public function init() {
        // Cargar clases dependientes
        $this->load_dependencies();

        // Registrar hooks
        $this->register_hooks();

        // Inicializar las clases dependientes
        $this->api->init();
        $this->licensing->init();

        // Inicializar la parte administrativa si estamos en el panel de admin
        if (is_admin()) {
            $admin = new Chatbot_Admin();
            $admin->init();
        }

        // Inicializar la parte pública
        $public = new Public_Display();
        $public->init();
    }

    /**
     * Carga las clases necesarias para el funcionamiento del plugin
     */
    private function load_dependencies() {
        // Cargar clases principales si no han sido cargadas vía autoloader
        if (!class_exists('\\AIChatbot\\Chatbot_API')) {
            require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-chatbot-api.php';
        }
        
        if (!class_exists('\\AIChatbot\\Chatbot_Licensing')) {
            require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-chatbot-licensing.php';
        }
        
        if (!class_exists('\\AIChatbot\\Chatbot_Admin')) {
            require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-chatbot-admin.php';
        }
        
        if (!class_exists('\\AIChatbot\\Chatbot_Analytics')) {
            require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-chatbot-analytics.php';
        }
        
        if (!class_exists('\\AIChatbot\\Public_Display')) {
            require_once AI_CHATBOT_PLUGIN_DIR . 'public/class-public-display.php';
        }

        // Inicializar instancias principales
        $this->api = new Chatbot_API();
        $this->licensing = new Chatbot_Licensing();
    }

    /**
     * Registra los hooks necesarios para el plugin
     */
    private function register_hooks() {
        // Hooks de activación/desactivación ya se registran en el archivo principal
        
        // Hooks para procesar mensajes AJAX
        add_action('wp_ajax_ai_chatbot_message', array($this, 'process_message'));
        add_action('wp_ajax_nopriv_ai_chatbot_message', array($this, 'process_message'));
        
        // Hook para exportar datos (solo admin)
        add_action('admin_init', array($this, 'handle_export_request'));
        
        // Hook para manejar feedback del chatbot
        add_action('wp_ajax_ai_chatbot_feedback', array($this, 'process_feedback'));
        add_action('wp_ajax_nopriv_ai_chatbot_feedback', array($this, 'process_feedback'));
    }

    /**
     * Procesa los mensajes del chatbot (callback AJAX)
     */
    public function process_message() {
        // Verificar nonce
        check_ajax_referer('ai_chatbot_nonce', 'nonce');
        
        $message = sanitize_text_field($_POST['message']);
        $session_id = sanitize_text_field($_POST['session_id']);
        
        // Verificar límites del plan
        $user_plan = $this->licensing->get_user_plan();
        $remaining = $this->licensing->get_remaining_queries();
        
        if ($remaining <= 0 && $user_plan != 'premium') {
            wp_send_json_error(array(
                'message' => __('Has alcanzado el límite de consultas para tu plan. Actualiza para continuar.', 'ai-chatbot-uniformes'),
                'upgrade' => true,
                'upgrade_link' => admin_url('admin.php?page=ai-chatbot-licensing')
            ));
            wp_die();
        }
        
        // Decrementar contador de consultas si no es premium
        if ($user_plan != 'premium') {
            $this->licensing->decrement_queries();
        }
        
        // Obtener historial para contexto
        $history = $this->get_conversation_history($session_id);
        
        // Verificar palabras bloqueadas (función premium)
        if ($this->licensing->is_feature_available('advanced_analytics')) {
            $blocked_words = get_option('ai_chatbot_blocked_words', '');
            if (!empty($blocked_words)) {
                $words_array = array_map('trim', explode(',', strtolower($blocked_words)));
                foreach ($words_array as $word) {
                    if (!empty($word) && strpos(strtolower($message), $word) !== false) {
                        wp_send_json_error(array(
                            'message' => __('Lo siento, no puedo responder a consultas que contengan ciertos términos prohibidos.', 'ai-chatbot-uniformes')
                        ));
                        wp_die();
                    }
                }
            }
        }
        
        // Enviar a la API de IA según el plan
        if ($user_plan == 'free') {
            $response = $this->get_basic_response($message);
        } else {
            $response = $this->api->get_ai_response($message, $history, $user_plan);
        }
        
        // Guardar conversación en la base de datos
        $this->save_conversation($message, $response, $session_id);
        
        // Registrar evento (analíticas premium)
        if ($this->licensing->is_feature_available('advanced_analytics')) {
            $analytics = new Chatbot_Analytics();
            $analytics->track_event('message_sent', [
                'message' => $message,
                'response' => $response,
                'plan' => $user_plan
            ]);
        }
        
        // Enviar respuesta
        wp_send_json_success(array(
            'message' => $response,
            'remaining' => $this->licensing->get_remaining_queries()
        ));
        
        wp_die();
    }
    
    /**
     * Procesa el feedback de los usuarios (callback AJAX)
     */
    public function process_feedback() {
        // Verificar nonce
        check_ajax_referer('ai_chatbot_nonce', 'nonce');
        
        if (!$this->licensing->is_feature_available('advanced_analytics')) {
            wp_send_json_error(['message' => 'Función no disponible en su plan']);
            wp_die();
        }
        
        $message_id = intval($_POST['message_id']);
        $rating = sanitize_text_field($_POST['rating']);
        $comment = isset($_POST['comment']) ? sanitize_textarea_field($_POST['comment']) : '';
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'ai_chatbot_feedback';
        
        // Insertar feedback
        $wpdb->insert(
            $table_name,
            [
                'message_id' => $message_id,
                'rating' => $rating,
                'comment' => $comment,
                'created_at' => current_time('mysql')
            ],
            ['%d', '%s', '%s', '%s']
        );
        
        if ($wpdb->insert_id) {
            wp_send_json_success(['message' => __('Gracias por tu feedback', 'ai-chatbot-uniformes')]);
        } else {
            wp_send_json_error(['message' => __('Error al guardar feedback', 'ai-chatbot-uniformes')]);
        }
        
        wp_die();
    }
    
    /**
     * Maneja solicitudes de exportación de datos
     */
    public function handle_export_request() {
        if (!isset($_GET['action']) || $_GET['action'] !== 'ai_chatbot_export_data') {
            return;
        }
        
        // Verificar si el usuario tiene permisos
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción.', 'ai-chatbot-uniformes'));
        }
        
        // Verificar nonce
        if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'ai_chatbot_export')) {
            wp_die(__('Error de seguridad. Por favor, intenta de nuevo.', 'ai-chatbot-uniformes'));
        }
        
        // Verificar si tiene plan premium
        if (!$this->licensing->is_feature_available('advanced_analytics')) {
            wp_die(__('Esta función solo está disponible en el plan Premium.', 'ai-chatbot-uniformes'));
        }
        
        // Obtener período
        $period = isset($_GET['period']) ? sanitize_text_field($_GET['period']) : 'month';
        
        // Crear instancia de analíticas y exportar
        $analytics = new Chatbot_Analytics();
        $file_url = $analytics->export_conversations_to_csv($period);
        
        if ($file_url) {
            // Redirigir al archivo
            wp_redirect($file_url);
            exit;
        } else {
            wp_die(__('No hay datos para exportar en el período seleccionado.', 'ai-chatbot-uniformes'));
        }
    }

    /**
     * Obtiene respuesta básica (plan gratuito)
     *
     * @param string $message Mensaje del usuario
     * @return string Respuesta del chatbot
     */
    private function get_basic_response($message) {
        // Respuestas predefinidas para preguntas comunes
        $responses = array(
            'precio' => __('Los precios de nuestros uniformes varían según el artículo y la talla. Por favor visita nuestro catálogo para más detalles.', 'ai-chatbot-uniformes'),
            'horario' => __('Nuestro horario de atención es de lunes a viernes de 9:00 a 18:00 y sábados de 9:00 a 13:00.', 'ai-chatbot-uniformes'),
            'entrega' => __('El tiempo de entrega estimado es de 2-3 días hábiles dentro de la ciudad.', 'ai-chatbot-uniformes'),
            'tallas' => __('Tenemos todas las tallas para niños desde preescolar hasta preparatoria. Si necesitas ayuda con las medidas, podemos guiarte.', 'ai-chatbot-uniformes'),
            'escuelas' => __('Trabajamos con varias escuelas de la zona. Por favor, indícanos el nombre de tu escuela para verificar si tenemos sus uniformes disponibles.', 'ai-chatbot-uniformes'),
            'devoluciones' => __('Aceptamos devoluciones dentro de los 30 días posteriores a la compra, siempre que las prendas estén en perfecto estado y con la etiqueta original.', 'ai-chatbot-uniformes'),
            'contacto' => __('Puedes contactarnos por teléfono al 555-123-4567 o por correo electrónico a info@uniformes-ejemplo.com', 'ai-chatbot-uniformes'),
            'pago' => __('Aceptamos efectivo, tarjetas de crédito/débito y transferencias bancarias.', 'ai-chatbot-uniformes'),
            'ubicacion' => __('Estamos ubicados en Av. Principal #123, Colonia Centro. Puedes encontrarnos en Google Maps buscando "Uniformes Escolares XYZ".', 'ai-chatbot-uniformes'),
            'descuentos' => __('Ofrecemos descuentos por volumen para compras de más de 3 uniformes completos. También tenemos promociones especiales al inicio del ciclo escolar.', 'ai-chatbot-uniformes'),
            'default' => __('Gracias por tu mensaje. Para obtener respuestas más detalladas, considera actualizar a nuestro plan Básico o Premium. Un asesor se pondrá en contacto contigo pronto.', 'ai-chatbot-uniformes')
        );
        
        $message_lower = strtolower($message);
        
        // Búsqueda simple de palabras clave
        foreach ($responses as $keyword => $response) {
            if (strpos($message_lower, $keyword) !== false) {
                return $response;
            }
        }
        
        // Si hay preguntas comunes que no hemos identificado con palabras clave
        if (strpos($message_lower, '?') !== false) {
            if (strpos($message_lower, 'cuando') !== false || 
                strpos($message_lower, 'hora') !== false || 
                strpos($message_lower, 'abr') !== false || 
                strpos($message_lower, 'cierr') !== false) {
                return $responses['horario'];
            }
            
            if (strpos($message_lower, 'donde') !== false || 
                strpos($message_lower, 'dirección') !== false || 
                strpos($message_lower, 'tienda') !== false || 
                strpos($message_lower, 'local') !== false) {
                return $responses['ubicacion'];
            }
        }
        
        // Respuesta por defecto
        return $responses['default'];
    }

    /**
     * Guarda conversación en la base de datos
     *
     * @param string $user_message Mensaje del usuario
     * @param string $bot_response Respuesta del bot
     * @param string $session_id ID de sesión
     */
    private function save_conversation($user_message, $bot_response, $session_id) {
        global $wpdb;
        
        $conversation_table = $wpdb->prefix . 'ai_chatbot_conversations';
        $messages_table = $wpdb->prefix . 'ai_chatbot_messages';
        
        // Obtener o crear la conversación
        $conversation = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM $conversation_table WHERE session_id = %s",
                $session_id
            )
        );
        
        $now = current_time('mysql');
        
        if (!$conversation) {
            // Crear nueva conversación
            $wpdb->insert(
                $conversation_table,
                array(
                    'session_id' => $session_id,
                    'user_id' => is_user_logged_in() ? get_current_user_id() : null,
                    'started_at' => $now,
                    'updated_at' => $now
                ),
                array('%s', '%d', '%s', '%s')
            );
            
            $conversation_id = $wpdb->insert_id;
        } else {
            $conversation_id = $conversation->id;
            
            // Actualizar timestamp
            $wpdb->update(
                $conversation_table,
                array('updated_at' => $now),
                array('id' => $conversation_id),
                array('%s'),
                array('%d')
            );
        }
        
        // Guardar mensaje del usuario
        $wpdb->insert(
            $messages_table,
            array(
                'conversation_id' => $conversation_id,
                'message_type' => 'user',
                'message' => $user_message,
                'created_at' => $now
            ),
            array('%d', '%s', '%s', '%s')
        );
        
        // Guardar respuesta del bot
        $wpdb->insert(
            $messages_table,
            array(
                'conversation_id' => $conversation_id,
                'message_type' => 'bot',
                'message' => $bot_response,
                'created_at' => $now
            ),
            array('%d', '%s', '%s', '%s')
        );
    }

    /**
     * Obtiene historial de conversación
     *
     * @param string $session_id ID de sesión
     * @return array Historial de mensajes
     */
    private function get_conversation_history($session_id) {
        global $wpdb;
        
        $conversation_table = $wpdb->prefix . 'ai_chatbot_conversations';
        $messages_table = $wpdb->prefix . 'ai_chatbot_messages';
        
        // Obtener ID de conversación
        $conversation = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM $conversation_table WHERE session_id = %s",
                $session_id
            )
        );
        
        if (!$conversation) {
            return array();
        }
        
        // Definir cuántos mensajes incluir en el contexto
        $context_size = absint(get_option('ai_chatbot_context_size', 10));
        
        // Obtener mensajes
        $messages = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT message_type, message, created_at 
                FROM $messages_table 
                WHERE conversation_id = %d 
                ORDER BY created_at ASC 
                LIMIT %d",
                $conversation->id,
                $context_size
            ),
            ARRAY_A
        );
        
        $history = array();
        
        foreach ($messages as $message) {
            $history[] = array(
                'role' => ($message['message_type'] === 'user') ? 'user' : 'assistant',
                'content' => $message['message']
            );
        }
        
        return $history;
    }

    /**
     * Devuelve la única instancia de esta clase (Singleton)
     *
     * @return Chatbot_Core
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
}