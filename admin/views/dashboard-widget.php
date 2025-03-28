<?php
/**
 * Plantilla para el widget de dashboard
 *
 * @package AIChatbot
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="ai-chatbot-dashboard-widget">
    <div class="ai-chatbot-widget-summary">
        <div class="ai-chatbot-widget-stat">
            <div class="ai-chatbot-widget-value"><?php echo esc_html(number_format_i18n($total_conversations)); ?></div>
            <div class="ai-chatbot-widget-label"><?php echo esc_html__('Conversaciones', 'ai-chatbot-uniformes'); ?></div>
        </div>
        
        <div class="ai-chatbot-widget-stat">
            <div class="ai-chatbot-widget-value"><?php echo esc_html(number_format_i18n($total_messages)); ?></div>
            <div class="ai-chatbot-widget-label"><?php echo esc_html__('Mensajes', 'ai-chatbot-uniformes'); ?></div>
        </div>
        
        <div class="ai-chatbot-widget-stat">
            <div class="ai-chatbot-widget-value"><?php echo esc_html(number_format_i18n($avg_messages_per_convo, 1)); ?></div>
            <div class="ai-chatbot-widget-label"><?php echo esc_html__('Msgs/Conv', 'ai-chatbot-uniformes'); ?></div>
        </div>
    </div>
    
    <div class="ai-chatbot-widget-plan">
        <div class="ai-chatbot-widget-plan-info">
            <div class="ai-chatbot-widget-plan-label">
                <?php echo esc_html__('Plan Actual:', 'ai-chatbot-uniformes'); ?>
                <span class="ai-chatbot-widget-plan-value"><?php echo esc_html(ucfirst($plan)); ?></span>
            </div>
            
            <?php if ($plan !== 'premium'): ?>
            <div class="ai-chatbot-widget-queries">
                <?php echo esc_html__('Consultas restantes:', 'ai-chatbot-uniformes'); ?>
                <span class="ai-chatbot-widget-queries-value"><?php echo esc_html(number_format_i18n($queries_left)); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($plan !== 'premium'): ?>
        <div class="ai-chatbot-widget-plan-action">
            <a href="<?php echo esc_url(admin_url('admin.php?page=ai-chatbot-licensing')); ?>" class="button button-small">
                <?php echo esc_html__('Actualizar', 'ai-chatbot-uniformes'); ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($plan !== 'free'): ?>
    <div class="ai-chatbot-widget-shortcuts">
        <a href="<?php echo esc_url(admin_url('admin.php?page=ai-chatbot-analytics')); ?>" class="ai-chatbot-widget-shortcut">
            <span class="dashicons dashicons-chart-bar"></span>
            <?php echo esc_html__('Ver Analíticas', 'ai-chatbot-uniformes'); ?>
        </a>
        
        <?php if ($this->licensing->is_feature_available('advanced_analytics')): ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=ai-chatbot-conversations')); ?>" class="ai-chatbot-widget-shortcut">
            <span class="dashicons dashicons-admin-comments"></span>
            <?php echo esc_html__('Ver Conversaciones', 'ai-chatbot-uniformes'); ?>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<style>
    .ai-chatbot-dashboard-widget {
        margin: -12px;
    }
    
    .ai-chatbot-widget-summary {
        display: flex;
        border-bottom: 1px solid #eee;
        margin-bottom: 12px;
    }
    
    .ai-chatbot-widget-stat {
        flex: 1;
        text-align: center;
        padding: 12px 8px;
    }
    
    .ai-chatbot-widget-stat:not(:last-child) {
        border-right: 1px solid #eee;
    }
    
    .ai-chatbot-widget-value {
        font-size: 18px;
        font-weight: 600;
        color: #3f51b5;
    }
    
    .ai-chatbot-widget-label {
        font-size: 12px;
        color: #666;
    }
    
    .ai-chatbot-widget-plan {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 12px 12px;
        border-bottom: 1px solid #eee;
        margin-bottom: 12px;
    }
    
    .ai-chatbot-widget-plan-value {
        font-weight: 600;
        text-transform: capitalize;
    }
    
    .ai-chatbot-widget-queries-value {
        font-weight: 600;
    }
    
    .ai-chatbot-widget-shortcuts {
        padding: 0 12px 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .ai-chatbot-widget-shortcut {
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        color: #3f51b5;
        font-size: 12px;
        border: 1px solid #3f51b5;
        border-radius: 3px;
        padding: 2px 8px;
    }
    
    .ai-chatbot-widget-shortcut:hover {
        background-color: #f0f3ff;
        color: #3f51b5;
    }
    
    .ai-chatbot-widget-shortcut .dashicons {
        font-size: 14px;
        width: 14px;
        height: 14px;
        margin-right: 4px;
    }
</style>