<?php
/**
 * Plugin Name: AI Chatbot para Uniformes
 * Plugin URI: https://tudominio.com/plugins/ai-chatbot
 * Description: Chatbot inteligente con planes de suscripción para tiendas de uniformes escolares
 * Version: 1.0.0
 * Author: Felipe Vega
 * Author URI: https://portfolio-felipevega.vercel.app/
 * Text Domain: ai-chatbot-uniformes
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Si este archivo es llamado directamente, abortar.
if (!defined('WPINC')) {
    die;
}

// Definir constantes del plugin
define('AI_CHATBOT_VERSION', '1.0.0');
define('AI_CHATBOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AI_CHATBOT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AI_CHATBOT_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoload de clases (si usas Composer)
if (file_exists(AI_CHATBOT_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once AI_CHATBOT_PLUGIN_DIR . 'vendor/autoload.php';
} else {
    // Carga manual de clases si no hay Composer
    require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-chatbot-core.php';
    require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-chatbot-api.php';
    require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-chatbot-licensing.php';
    require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-chatbot-admin.php';
    require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-chatbot-analytics.php';
    require_once AI_CHATBOT_PLUGIN_DIR . 'public/class-public-display.php';
}

/**
 * Activación del plugin
 */
function ai_chatbot_activate() {
    // Crear tablas personalizadas si son necesarias
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Tabla para conversaciones
    $table_conversations = $wpdb->prefix . 'ai_chatbot_conversations';
    
    $sql = "CREATE TABLE $table_conversations (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        session_id varchar(50) NOT NULL,
        user_id bigint(20) DEFAULT NULL,
        started_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY session_id (session_id),
        KEY user_id (user_id)
    ) $charset_collate;";
    
    // Tabla para mensajes
    $table_messages = $wpdb->prefix . 'ai_chatbot_messages';
    
    $sql .= "CREATE TABLE $table_messages (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        conversation_id bigint(20) NOT NULL,
        message_type varchar(20) NOT NULL,
        message text NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY conversation_id (conversation_id)
    ) $charset_collate;";
    
    // Tabla para eventos (analíticas avanzadas)
    $table_events = $wpdb->prefix . 'ai_chatbot_events';
    
    $sql .= "CREATE TABLE $table_events (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        event_type varchar(50) NOT NULL,
        event_data longtext DEFAULT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY event_type (event_type),
        KEY created_at (created_at)
    ) $charset_collate;";
    
    // Tabla para feedback (valoración de respuestas)
    $table_feedback = $wpdb->prefix . 'ai_chatbot_feedback';
    
    $sql .= "CREATE TABLE $table_feedback (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        message_id bigint(20) NOT NULL,
        rating varchar(20) NOT NULL,
        comment text DEFAULT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY message_id (message_id)
    ) $charset_collate;";
    
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    
    // Establecer opciones por defecto
    $default_options = [
        'ai_chatbot_current_plan' => 'free',
        'ai_chatbot_queries_used' => 0,
        'ai_chatbot_api_key' => '',
        'ai_chatbot_model' => 'gpt-3.5-turbo',
        'ai_chatbot_show_in_footer' => 'yes',
        'ai_chatbot_avoid_duplicate' => 'yes',
        'ai_chatbot_title' => __('Asistente de Uniformes', 'ai-chatbot-uniformes'),
        'ai_chatbot_welcome_message' => __('¡Hola! 👋 Soy el asistente virtual de Uniformes Escolares. ¿En qué puedo ayudarte hoy?', 'ai-chatbot-uniformes'),
        'ai_chatbot_primary_color' => '#3f51b5',
    ];
    
    foreach ($default_options as $option => $value) {
        if (get_option($option) === false) {
            add_option($option, $value);
        }
    }
    
    // Programar reinicio mensual de límites
    if (!wp_next_scheduled('ai_chatbot_reset_limits')) {
        wp_schedule_event(time(), 'monthly', 'ai_chatbot_reset_limits');
    }
    
    // Vaciar rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'ai_chatbot_activate');

/**
 * Desactivación del plugin
 */
function ai_chatbot_deactivate() {
    // Limpiar tareas programadas
    wp_clear_scheduled_hook('ai_chatbot_reset_limits');
    
    // Vaciar rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'ai_chatbot_deactivate');

/**
 * Inicialización del plugin
 */
function ai_chatbot_init() {
    // Cargar traducciones
    load_plugin_textdomain('ai-chatbot-uniformes', false, dirname(plugin_basename(__FILE__)) . '/languages');
    
    // Inicializar el núcleo del plugin
    $core = new \AIChatbot\Chatbot_Core(); // Nota el backslash al principio
    $core->init();
    
    // Inicializar la parte administrativa
    if (is_admin()) {
        $admin = new \AIChatbot\Chatbot_Admin(); // Nota el backslash al principio
        $admin->init();
    }
    
    // Inicializar el frontend
    $public = new \AIChatbot\Public_Display(); // Nota el backslash al principio
    $public->init();
}
add_action('plugins_loaded', 'ai_chatbot_init');

/**
 * Registrar un intervalo de cron personalizado para reinicio mensual
 *
 * @param array $schedules Programaciones existentes
 * @return array Programaciones modificadas
 */
function ai_chatbot_add_cron_interval($schedules) {
    $schedules['monthly'] = [
        'interval' => 30 * 24 * 60 * 60, // 30 días
        'display' => __('Una vez al mes', 'ai-chatbot-uniformes')
    ];
    return $schedules;
}
add_filter('cron_schedules', 'ai_chatbot_add_cron_interval');