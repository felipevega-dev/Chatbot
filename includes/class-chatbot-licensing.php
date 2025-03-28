<?php
/**
 * Clase para manejar licencias y suscripciones
 *
 * @package AIChatbot
 */

namespace AIChatbot;

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase que maneja las licencias y suscripciones
 */
class Chatbot_Licensing {

    /**
     * Planes disponibles
     *
     * @var array
     */
    private $available_plans = ['free', 'basic', 'premium'];

    /**
     * Límites de consultas por plan
     *
     * @var array
     */
    private $query_limits = [
        'free' => 20,
        'basic' => 100,
        'premium' => 9999 // Ilimitado
    ];

    /**
     * Inicializa la clase de licencias
     */
    public function init() {
        // Programar reinicio mensual de límites
        if (!wp_next_scheduled('ai_chatbot_reset_limits')) {
            wp_schedule_event(time(), 'monthly', 'ai_chatbot_reset_limits');
        }

        // Hook para el reinicio de límites
        add_action('ai_chatbot_reset_limits', [$this, 'reset_query_limits']);
        
        // Añadir soporte para WooCommerce Subscriptions si está disponible
        if (class_exists('WC_Subscriptions')) {
            add_action('woocommerce_subscription_status_updated', [$this, 'handle_subscription_change'], 10, 3);
        }
    }

    /**
     * Obtiene el plan actual del usuario
     *
     * @return string Plan actual ('free', 'basic', 'premium')
     */
    public function get_user_plan() {
        $plan = get_option('ai_chatbot_current_plan', 'free');
        
        // Verificar que sea un plan válido
        if (!in_array($plan, $this->available_plans)) {
            $plan = 'free';
        }
        
        return $plan;
    }

    /**
     * Obtiene las consultas restantes según el plan
     *
     * @return int Número de consultas restantes
     */
    public function get_remaining_queries() {
        $plan = $this->get_user_plan();
        
        // Si es premium, devolver "ilimitado"
        if ($plan === 'premium') {
            return $this->query_limits['premium'];
        }
        
        // Obtener límite según plan
        $limit = $this->query_limits[$plan] ?? $this->query_limits['free'];
        
        // Obtener consultas usadas
        $used = get_option('ai_chatbot_queries_used', 0);
        
        // Calcular restantes (mínimo 0)
        return max(0, $limit - $used);
    }

    /**
     * Decrementa el contador de consultas
     *
     * @return int Consultas restantes
     */
    public function decrement_queries() {
        $used = get_option('ai_chatbot_queries_used', 0);
        update_option('ai_chatbot_queries_used', $used + 1);
        
        return $this->get_remaining_queries();
    }

    /**
     * Reinicia los límites de consultas (ejecutado mensualmente)
     */
    public function reset_query_limits() {
        update_option('ai_chatbot_queries_used', 0);
    }

    /**
     * Establece un nuevo plan
     *
     * @param string $plan Nuevo plan ('free', 'basic', 'premium')
     * @return bool Éxito de la operación
     */
    public function set_plan($plan) {
        // Verificar que sea un plan válido
        if (!in_array($plan, $this->available_plans)) {
            return false;
        }
        
        // Actualizar plan
        update_option('ai_chatbot_current_plan', $plan);
        
        // Si cambia a un plan superior, reiniciar contador de consultas
        $this->reset_query_limits();
        
        return true;
    }

    /**
     * Maneja cambios en las suscripciones de WooCommerce
     *
     * @param WC_Subscription $subscription Objeto de suscripción
     * @param string $new_status Nuevo estado
     * @param string $old_status Antiguo estado
     */
    public function handle_subscription_change($subscription, $new_status, $old_status) {
        // Verificar si la suscripción está activa
        if ($new_status === 'active' && $old_status !== 'active') {
            // Obtener productos de la suscripción
            $items = $subscription->get_items();
            
            foreach ($items as $item) {
                $product_id = $item->get_product_id();
                
                // Verificar qué plan corresponde al producto
                $plan = $this->get_plan_from_product_id($product_id);
                
                if ($plan) {
                    $this->set_plan($plan);
                    break;
                }
            }
        } else if ($new_status !== 'active' && $old_status === 'active') {
            // Si la suscripción se cancela/pausa, volver al plan gratuito
            $this->set_plan('free');
        }
    }

    /**
     * Obtiene el plan correspondiente a un ID de producto
     *
     * @param int $product_id ID del producto
     * @return string|false Plan correspondiente o false si no hay coincidencia
     */
    private function get_plan_from_product_id($product_id) {
        // Estos IDs deben configurarse según los productos reales
        $plan_product_ids = [
            'basic' => get_option('ai_chatbot_basic_product_id', 0),
            'premium' => get_option('ai_chatbot_premium_product_id', 0)
        ];
        
        foreach ($plan_product_ids as $plan => $id) {
            if ((int) $id === (int) $product_id) {
                return $plan;
            }
        }
        
        return false;
    }

    /**
     * Verifica si una característica está disponible en el plan actual
     *
     * @param string $feature Nombre de la característica
     * @return bool Si la característica está disponible
     */
    public function is_feature_available($feature) {
        $current_plan = $this->get_user_plan();
        
        // Mapa de características por plan
        $feature_map = [
            'ai_responses' => ['basic', 'premium'],
            'unlimited_queries' => ['premium'],
            'advanced_analytics' => ['premium'],
            'woocommerce_integration' => ['basic', 'premium'],
            'customization' => ['premium']
        ];
        
        // Verificar si la característica existe y está disponible en el plan actual
        if (isset($feature_map[$feature])) {
            return in_array($current_plan, $feature_map[$feature]);
        }
        
        // Por defecto, las características no especificadas no están disponibles
        return false;
    }
}