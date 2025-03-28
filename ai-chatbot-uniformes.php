<?php
/**
 * Plugin Name: AI Chatbot para Uniformes
 * Plugin URI: https://tudominio.com/plugins/ai-chatbot
 * Description: Chatbot inteligente con planes de suscripción para tiendas de uniformes escolares
 * Version: 1.0.0
 * Author: Tu Nombre
 * Author URI: https://tudominio.com
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
}

// Activación del plugin
register_activation_hook(__FILE__, 'ai_chatbot_activate');
function ai_chatbot_activate() {
    // Código de activación
}

// Desactivación del plugin
register_deactivation_hook(__FILE__, 'ai_chatbot_deactivate');
function ai_chatbot_deactivate() {
    // Código de desactivación
}

// Iniciar el plugin
function ai_chatbot_init() {
    // Cargar archivos principales
    require_once AI_CHATBOT_PLUGIN_DIR . 'includes/class-chatbot-core.php';
    
    // Inicializar el plugin
    $chatbot = new AIChatbot\Chatbot_Core();
    $chatbot->init();
}
add_action('plugins_loaded', 'ai_chatbot_init');