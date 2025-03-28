<?php
/**
 * Clase para manejar los menús de administración
 *
 * @package AIChatbot
 */

namespace AIChatbot\Admin;

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase que maneja los menús y submenús en el panel de administración
 */
class Admin_Menu {

    /**
     * Instancia de la clase de licencias
     *
     * @var \AIChatbot\Chatbot_Licensing
     */
    private $licensing;

    /**
     * Constructor
     */
    public function __construct() {
        // Obtener instancia de licencias
        $this->licensing = new \AIChatbot\Chatbot_Licensing();
    }

    /**
     * Inicializa los menús de administración
     */
    public function init() {
        // Agregar los menús a WordPress
        add_action('admin_menu', [$this, 'register_menus']);
        
        // Agregar enlace de configuración en la página de plugins
        add_filter('plugin_action_links_' . AI_CHATBOT_PLUGIN_BASENAME, [$this, 'add_plugin_action_links']);
    }

    /**
     * Registra los menús y submenús en el panel de administración
     */
    public function register_menus() {
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
        
        // Submenú: Conversaciones (solo para planes pagos)
        if ($this->licensing->is_feature_available('advanced_analytics')) {
            add_submenu_page(
                'ai-chatbot-settings',
                __('Conversaciones', 'ai-chatbot-uniformes'),
                __('Conversaciones', 'ai-chatbot-uniformes'),
                'manage_options',
                'ai-chatbot-conversations',
                [$this, 'render_conversations_page']
            );
        }
        
        // Submenú: Documentación
        add_submenu_page(
            'ai-chatbot-settings',
            __('Documentación', 'ai-chatbot-uniformes'),
            __('Documentación', 'ai-chatbot-uniformes'),
            'manage_options',
            'ai-chatbot-documentation',
            [$this, 'render_documentation_page']
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
     * Renderiza la página de conversaciones
     */
    public function render_conversations_page() {
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Verificar si tiene plan apropiado
        if (!$this->licensing->is_feature_available('advanced_analytics')) {
            wp_redirect(admin_url('admin.php?page=ai-chatbot-licensing'));
            exit;
        }
        
        // Verificar si estamos viendo una conversación específica
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            // Incluir plantilla de detalle de conversación
            include AI_CHATBOT_PLUGIN_DIR . 'admin/views/conversation-detail.php';
        } else {
            // Incluir plantilla de lista de conversaciones
            include AI_CHATBOT_PLUGIN_DIR . 'admin/views/conversations.php';
        }
    }

    /**
     * Renderiza la página de documentación
     */
    public function render_documentation_page() {
        // Verificar permisos
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Incluir plantilla
        include AI_CHATBOT_PLUGIN_DIR . 'admin/views/documentation.php';
    }
}