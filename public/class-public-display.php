<?php
/**
 * Clase para manejar la visualización pública del chatbot
 *
 * @package AIChatbot
 */

namespace AIChatbot;

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase que maneja la visualización del chatbot en el frontend
 */
class Public_Display {

    /**
     * Instancia de la clase de licencias
     *
     * @var Chatbot_Licensing
     */
    private $licensing;

    /**
     * Constructor
     */
    public function __construct() {
        $this->licensing = new Chatbot_Licensing();
    }

    /**
     * Inicializa la visualización pública del plugin
     */
    public function init() {
        // Registrar scripts y estilos
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        
        // Añadir shortcode para insertar el chatbot
        add_shortcode('ai_chatbot', [$this, 'chatbot_shortcode']);
        
        // Añadir el chatbot al footer del sitio (si está habilitado)
        if (get_option('ai_chatbot_show_in_footer', 'yes') === 'yes') {
            add_action('wp_footer', [$this, 'render_chatbot']);
        }
    }

    /**
     * Registra y carga los scripts y estilos necesarios
     */
    public function enqueue_assets() {
        // Estilos
        wp_enqueue_style(
            'ai-chatbot-styles',
            AI_CHATBOT_PLUGIN_URL . 'public/css/chatbot.css',
            [],
            AI_CHATBOT_VERSION
        );
        
        // Scripts
        wp_enqueue_script(
            'ai-chatbot-script',
            AI_CHATBOT_PLUGIN_URL . 'public/js/chatbot.js',
            ['jquery'],
            AI_CHATBOT_VERSION,
            true
        );
        
        // Pasar variables a JavaScript
        wp_localize_script(
            'ai-chatbot-script',
            'aiChatbotData',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ai_chatbot_nonce'),
                'user_plan' => $this->licensing->get_user_plan(),
                'remaining_queries' => $this->licensing->get_remaining_queries(),
                'plugin_url' => AI_CHATBOT_PLUGIN_URL,
                'strings' => [
                    'welcome_message' => get_option(
                        'ai_chatbot_welcome_message',
                        __('¡Hola! 👋 Soy el asistente virtual de Uniformes Escolares. ¿En qué puedo ayudarte hoy?', 'ai-chatbot-uniformes')
                    ),
                    'typing_indicator' => __('Escribiendo...', 'ai-chatbot-uniformes'),
                    'send_message' => __('Enviar', 'ai-chatbot-uniformes'),
                    'input_placeholder' => __('Escribe tu pregunta aquí...', 'ai-chatbot-uniformes'),
                    'upgrade_message' => __('Actualiza tu plan', 'ai-chatbot-uniformes'),
                    'error_message' => __('Ha ocurrido un error. Por favor, intenta de nuevo.', 'ai-chatbot-uniformes')
                ],
                'appearance' => [
                    'primary_color' => get_option('ai_chatbot_primary_color', '#3f51b5'),
                    'title' => get_option('ai_chatbot_title', __('Asistente de Uniformes', 'ai-chatbot-uniformes'))
                ]
            ]
        );
        
        // Añadir estilos personalizados
        $this->add_custom_styles();
    }

    /**
     * Añade estilos personalizados según la configuración
     */
    private function add_custom_styles() {
        $primary_color = get_option('ai_chatbot_primary_color', '#3f51b5');
        
        $custom_css = "
            #ai-chatbot-toggle, .ai-chatbot-header {
                background-color: {$primary_color};
            }
            #ai-chatbot-toggle:hover {
                background-color: " . $this->adjust_brightness($primary_color, -20) . ";
            }
            .ai-chatbot-user-message .ai-chatbot-message-content {
                background-color: {$primary_color};
            }
            .ai-chatbot-input button, .ai-chatbot-upgrade {
                color: {$primary_color};
            }
            .ai-chatbot-upgrade {
                background-color: {$primary_color};
            }
            .ai-chatbot-upgrade:hover {
                background-color: " . $this->adjust_brightness($primary_color, -20) . ";
            }
        ";
        
        wp_add_inline_style('ai-chatbot-styles', $custom_css);
    }

    /**
     * Ajusta el brillo de un color hex
     *
     * @param string $hex Color en formato hexadecimal
     * @param int $steps Pasos para ajustar (-255 a 255)
     * @return string Color ajustado en formato hexadecimal
     */
    private function adjust_brightness($hex, $steps) {
        // Convertir hex a rgb
        $hex = str_replace('#', '', $hex);
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Ajustar brillo
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        
        // Convertir de vuelta a hex
        return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Callback para el shortcode [ai_chatbot]
     *
     * @param array $atts Atributos del shortcode
     * @return string HTML del chatbot
     */
    public function chatbot_shortcode($atts) {
        // Extraer atributos
        $atts = shortcode_atts(
            [
                'title' => get_option('ai_chatbot_title', __('Asistente de Uniformes', 'ai-chatbot-uniformes')),
                'welcome_message' => get_option('ai_chatbot_welcome_message', ''),
                'position' => 'inline', // inline o fixed
            ],
            $atts,
            'ai_chatbot'
        );
        
        // Si es inline, agregar clase adicional
        $class = ($atts['position'] === 'inline') ? 'ai-chatbot-inline' : '';
        
        // Iniciar buffer de salida
        ob_start();
        
        // Incluir plantilla
        include AI_CHATBOT_PLUGIN_DIR . 'public/views/chatbot-template.php';
        
        // Devolver HTML
        return ob_get_clean();
    }

    /**
     * Renderiza el chatbot en el footer
     */
    public function render_chatbot() {
        // No mostrar si ya se ha insertado con shortcode y está configurado para no duplicar
        if (get_option('ai_chatbot_avoid_duplicate', 'yes') === 'yes' && $this->is_shortcode_present()) {
            return;
        }
        
        // Configurar atributos por defecto
        $atts = [
            'title' => get_option('ai_chatbot_title', __('Asistente de Uniformes', 'ai-chatbot-uniformes')),
            'welcome_message' => get_option('ai_chatbot_welcome_message', ''),
            'position' => 'fixed',
        ];
        
        // Incluir plantilla
        include AI_CHATBOT_PLUGIN_DIR . 'public/views/chatbot-template.php';
    }

    /**
     * Verifica si el shortcode del chatbot está presente en la página actual
     *
     * @return bool Si el shortcode está presente
     */
    private function is_shortcode_present() {
        global $post;
        
        if (!is_a($post, 'WP_Post')) {
            return false;
        }
        
        return has_shortcode($post->post_content, 'ai_chatbot');
    }

    /**
     * Registra eventos para analíticas
     *
     * @param string $event_type Tipo de evento
     * @param array $event_data Datos del evento
     */
    public function track_event($event_type, $event_data = []) {
        // Esta función solo está disponible en planes pagos
        if (!$this->licensing->is_feature_available('advanced_analytics')) {
            return;
        }
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ai_chatbot_events';
        
        // Asegurarse de que la tabla existe
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");
        
        if (!$table_exists) {
            return;
        }
        
        // Datos comunes para todos los eventos
        $common_data = [
            'session_id' => isset($_COOKIE['ai_chatbot_session']) ? sanitize_text_field($_COOKIE['ai_chatbot_session']) : '',
            'user_id' => get_current_user_id(),
            'page_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'ip_address' => $this->get_client_ip(),
        ];
        
        // Combinar datos
        $event_data = array_merge($event_data, $common_data);
        
        // Insertar evento
        $wpdb->insert(
            $table_name,
            [
                'event_type' => $event_type,
                'event_data' => wp_json_encode($event_data),
                'created_at' => current_time('mysql')
            ],
            [
                '%s',
                '%s',
                '%s'
            ]
        );
    }

    /**
     * Obtiene la IP del cliente
     *
     * @return string Dirección IP
     */
    private function get_client_ip() {
        $ip_address = '';
        
        // Verificar si la IP está en $_SERVER
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Verificar si la IP está detrás de un proxy
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            // Obtener IP directamente
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip_address;
    }
}