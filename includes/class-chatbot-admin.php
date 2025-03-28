<?php
/**
 * Clase para manejar la parte administrativa del chatbot
 *
 * @package AIChatbot
 */

namespace AIChatbot;

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase que maneja la interfaz administrativa del plugin
 */
class Chatbot_Admin {

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
     * Inicializa la parte administrativa del plugin
     */
    public function init() {
        // Agregar menú de administración
        add_action('admin_menu', [$this, 'add_admin_menu']);
        
        // Registrar ajustes
        add_action('admin_init', [$this, 'register_settings']);
        
        // Cargar scripts y estilos para admin
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        
        // Añadir enlace de configuración en la página de plugins
        add_filter('plugin_action_links_' . AI_CHATBOT_PLUGIN_BASENAME, [$this, 'add_plugin_action_links']);
        
        // Añadir meta box para analíticas en el dashboard
        add_action('wp_dashboard_setup', [$this, 'add_dashboard_widget']);
    }

    /**
     * Agrega el menú de administración del plugin
     */
    public function add_admin_menu() {
        // Menú principal
        add_menu_page(
            __('AI Chatbot para Uniformes', 'ai-chatbot-uniformes'),
            __('AI Chatbot', 'ai-chatbot-uniformes'),
            'manage_options',
            'ai-chatbot-settings',
            [$this, 'render_settings_page'],
            'dashicons-format-chat',
            25
        );
        
        // Submenú: Configuración (mismo que el principal)
        add_submenu_page(
            'ai-chatbot-settings',
            __('Configuración', 'ai-chatbot-uniformes'),
            __('Configuración', 'ai-chatbot-uniformes'),
            'manage_options',
            'ai-chatbot-settings',
            [$this, 'render_settings_page']
        );
        
        // Submenú: Analíticas
        add_submenu_page(
            'ai-chatbot-settings',
            __('Analíticas', 'ai-chatbot-uniformes'),
            __('Analíticas', 'ai-chatbot-uniformes'),
            'manage_options',
            'ai-chatbot-analytics',
            [$this, 'render_analytics_page']
        );
        
        // Submenú: Licencias y suscripciones
        add_submenu_page(
            'ai-chatbot-settings',
            __('Licencias', 'ai-chatbot-uniformes'),
            __('Licencias', 'ai-chatbot-uniformes'),
            'manage_options',
            'ai-chatbot-licensing',
            [$this, 'render_licensing_page']
        );
    }

    /**
     * Registra los ajustes del plugin
     */
    public function register_settings() {
        // Grupo General
        register_setting('ai_chatbot_general', 'ai_chatbot_api_key');
        register_setting('ai_chatbot_general', 'ai_chatbot_model');
        register_setting('ai_chatbot_general', 'ai_chatbot_show_in_footer');
        register_setting('ai_chatbot_general', 'ai_chatbot_avoid_duplicate');
        
        // Grupo Apariencia
        register_setting('ai_chatbot_appearance', 'ai_chatbot_title');
        register_setting('ai_chatbot_appearance', 'ai_chatbot_welcome_message');
        register_setting('ai_chatbot_appearance', 'ai_chatbot_primary_color');
        
        // Grupo Licencia
        register_setting('ai_chatbot_licensing', 'ai_chatbot_license_key');
        register_setting('ai_chatbot_licensing', 'ai_chatbot_license_status');
        register_setting('ai_chatbot_licensing', 'ai_chatbot_basic_product_id');
        register_setting('ai_chatbot_licensing', 'ai_chatbot_premium_product_id');
    }

    /**
     * Carga scripts y estilos para el admin
     *
     * @param string $hook Hook actual
     */
    public function enqueue_admin_assets($hook) {
        // Solo cargar en las páginas del plugin
        if (strpos($hook, 'ai-chatbot') === false) {
            return;
        }
        
        // Estilos
        wp_enqueue_style(
            'ai-chatbot-admin',
            AI_CHATBOT_PLUGIN_URL . 'admin/css/admin.css',
            [],
            AI_CHATBOT_VERSION
        );
        
        // Color picker
        wp_enqueue_style('wp-color-picker');
        
        // Scripts
        wp_enqueue_script(
            'ai-chatbot-admin',
            AI_CHATBOT_PLUGIN_URL . 'admin/js/admin.js',
            ['jquery', 'wp-color-picker'],
            AI_CHATBOT_VERSION,
            true
        );
        
        // Pasar variables a JavaScript
        wp_localize_script(
            'ai-chatbot-admin',
            'aiChatbotAdmin',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ai_chatbot_admin_nonce'),
                'strings' => [
                    'save_success' => __('Ajustes guardados correctamente.', 'ai-chatbot-uniformes'),
                    'save_error' => __('Error al guardar los ajustes.', 'ai-chatbot-uniformes'),
                    'confirm_reset' => __('¿Estás seguro de que deseas restablecer todos los ajustes a sus valores predeterminados?', 'ai-chatbot-uniformes')
                ]
            ]
        );
    }

    /**
     * Añade enlaces de acción en la página de plugins
     *
     * @param array $links Enlaces existentes
     * @return array Enlaces modificados
     */
    public function add_plugin_action_links($links) {
        $plugin_links = [
            '<a href="' . admin_url('admin.php?page=ai-chatbot-settings') . '">' . __('Configuración', 'ai-chatbot-uniformes') . '</a>',
        ];
        
        if ($this->licensing->get_user_plan() !== 'premium') {
            $plugin_links[] = '<a href="' . admin_url('admin.php?page=ai-chatbot-licensing') . '" style="color: #3f51b5; font-weight: bold;">' . __('Actualizar a Premium', 'ai-chatbot-uniformes') . '</a>';
        }
        
        return array_merge($plugin_links, $links);
    }

    /**
     * Añade un widget al dashboard para analíticas rápidas
     */
    public function add_dashboard_widget() {
        // Solo mostrar si el usuario tiene permiso
        if (!current_user_can('manage_options')) {
            return;
        }
        
        wp_add_dashboard_widget(
            'ai_chatbot_dashboard_widget',
            __('AI Chatbot - Estadísticas Rápidas', 'ai-chatbot-uniformes'),
            [$this, 'render_dashboard_widget']
        );
    }

    /**
     * Renderiza la página de configuración
     */
    public function render_settings_page() {
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Incluir plantilla
        include AI_CHATBOT_PLUGIN_DIR . 'admin/views/settings.php';
    }

    /**
     * Renderiza la página de analíticas
     */
    public function render_analytics_page() {
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Verificar si la función de analíticas avanzadas está disponible
        $has_analytics = $this->licensing->is_feature_available('advanced_analytics');
        
        // Incluir plantilla
        include AI_CHATBOT_PLUGIN_DIR . 'admin/views/analytics.php';
    }

    /**
     * Renderiza la página de licencias
     */
    public function render_licensing_page() {
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Incluir plantilla
        include AI_CHATBOT_PLUGIN_DIR . 'admin/views/licensing.php';
    }

    /**
     * Renderiza el widget del dashboard
     */
    public function render_dashboard_widget() {
        // Crear instancia de la clase de analíticas
        $analytics = new Chatbot_Analytics();
        
        // Obtener datos básicos
        $total_conversations = $analytics->get_total_conversations();
        $total_messages = $analytics->get_total_messages();
        $avg_messages_per_convo = ($total_conversations > 0) ? round($total_messages / $total_conversations, 1) : 0;
        $plan = $this->licensing->get_user_plan();
        $queries_left = $this->licensing->get_remaining_queries();
        
        // Incluir plantilla
        include AI_CHATBOT_PLUGIN_DIR . 'admin/views/dashboard-widget.php';
    }
}