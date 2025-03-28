<?php
/**
 * Plantilla para la página de analíticas del chatbot
 *
 * @package AIChatbot
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Crear instancia de la clase de analíticas
$analytics = new AIChatbot\Chatbot_Analytics();

// Verificar si el usuario tiene acceso a analíticas avanzadas
$has_advanced_analytics = $this->licensing->is_feature_available('advanced_analytics');

// Obtener período seleccionado (por defecto: mes)
$period = isset($_GET['period']) ? sanitize_text_field($_GET['period']) : 'month';
$valid_periods = ['today', 'week', 'month', 'all'];
if (!in_array($period, $valid_periods)) {
    $period = 'month';
}

// Obtener datos básicos de analíticas
$total_conversations = $analytics->get_total_conversations($period);
$total_messages = $analytics->get_total_messages($period);
$avg_messages_per_convo = ($total_conversations > 0) ? round($total_messages / $total_conversations, 1) : 0;
$conversations_by_day = $analytics->get_conversations_by_day(30);

// Datos avanzados (solo disponibles en plan premium)
$popular_topics = $has_advanced_analytics ? $analytics->get_popular_topics(10) : [];
$frequent_queries = $analytics->get_frequent_queries(10, $period);
$satisfaction_data = $has_advanced_analytics ? $analytics->get_satisfaction_rate($period) : ['rate' => 0];
$response_time = $has_advanced_analytics ? $analytics->get_average_response_time($period) : 0;
?>

<div class="wrap">
    <h1><?php echo esc_html__('AI Chatbot para Uniformes - Analíticas', 'ai-chatbot-uniformes'); ?></h1>
    
    <div class="ai-chatbot-analytics-header">
        <div class="ai-chatbot-period-selector">
            <form method="get">
                <input type="hidden" name="page" value="ai-chatbot-analytics">
                <select name="period" onchange="this.form.submit()">
                    <option value="today" <?php selected($period, 'today'); ?>><?php echo esc_html__('Hoy', 'ai-chatbot-uniformes'); ?></option>
                    <option value="week" <?php selected($period, 'week'); ?>><?php echo esc_html__('Última semana', 'ai-chatbot-uniformes'); ?></option>
                    <option value="month" <?php selected($period, 'month'); ?>><?php echo esc_html__('Último mes', 'ai-chatbot-uniformes'); ?></option>
                    <option value="all" <?php selected($period, 'all'); ?>><?php echo esc_html__('Todo el tiempo', 'ai-chatbot-uniformes'); ?></option>
                </select>
            </form>
        </div>
        
        <?php if ($has_advanced_analytics): ?>
        <div class="ai-chatbot-export-button">
            <a href="<?php echo esc_url(add_query_arg([
                'action' => 'ai_chatbot_export_data',
                'period' => $period,
                'nonce' => wp_create_nonce('ai_chatbot_export')
            ])); ?>" class="button">
                <span class="dashicons dashicons-download"></span>
                <?php echo esc_html__('Exportar Datos', 'ai-chatbot-uniformes'); ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if (!$has_advanced_analytics): ?>
    <div class="ai-chatbot-premium-notice">
        <p>
            <span class="dashicons dashicons-lock"></span>
            <?php echo esc_html__('Desbloquea analíticas avanzadas, exportación de datos y más características actualizando al', 'ai-chatbot-uniformes'); ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=ai-chatbot-licensing')); ?>"><?php echo esc_html__('Plan Premium', 'ai-chatbot-uniformes'); ?></a>.
        </p>
    </div>
    <?php endif; ?>
    
    <!-- Resumen de Datos -->
    <div class="ai-chatbot-analytics-summary">
        <div class="ai-chatbot-metric-card">
            <div class="ai-chatbot-metric-icon">
                <span class="dashicons dashicons-format-chat"></span>
            </div>
            <div class="ai-chatbot-metric-content">
                <div class="ai-chatbot-metric-value"><?php echo esc_html(number_format_i18n($total_conversations)); ?></div>
                <div class="ai-chatbot-metric-label"><?php echo esc_html__('Conversaciones', 'ai-chatbot-uniformes'); ?></div>
            </div>
        </div>
        
        <div class="ai-chatbot-metric-card">
            <div class="ai-chatbot-metric-icon">
                <span class="dashicons dashicons-admin-comments"></span>
            </div>
            <div class="ai-chatbot-metric-content">
                <div class="ai-chatbot-metric-value"><?php echo esc_html(number_format_i18n($total_messages)); ?></div>
                <div class="ai-chatbot-metric-label"><?php echo esc_html__('Mensajes', 'ai-chatbot-uniformes'); ?></div>
            </div>
        </div>
        
        <div class="ai-chatbot-metric-card">
            <div class="ai-chatbot-metric-icon">
                <span class="dashicons dashicons-chart-line"></span>
            </div>
            <div class="ai-chatbot-metric-content">
                <div class="ai-chatbot-metric-value"><?php echo esc_html(number_format_i18n($avg_messages_per_convo)); ?></div>
                <div class="ai-chatbot-metric-label"><?php echo esc_html__('Mensajes por conversación', 'ai-chatbot-uniformes'); ?></div>
            </div>
        </div>
        
        <?php if ($has_advanced_analytics): ?>
        <div class="ai-chatbot-metric-card">
            <div class="ai-chatbot-metric-icon">
                <span class="dashicons dashicons-thumbs-up"></span>
            </div>
            <div class="ai-chatbot-metric-content">
                <div class="ai-chatbot-metric-value"><?php echo esc_html(number_format_i18n($satisfaction_data['rate'])); ?>%</div>
                <div class="ai-chatbot-metric-label"><?php echo esc_html__('Satisfacción', 'ai-chatbot-uniformes'); ?></div>
            </div>
        </div>
        
        <div class="ai-chatbot-metric-card">
            <div class="ai-chatbot-metric-icon">
                <span class="dashicons dashicons-clock"></span>
            </div>
            <div class="ai-chatbot-metric-content">
                <div class="ai-chatbot-metric-value"><?php echo esc_html(number_format_i18n($response_time)); ?>s</div>
                <div class="ai-chatbot-metric-label"><?php echo esc_html__('Tiempo de respuesta', 'ai-chatbot-uniformes'); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="ai-chatbot-analytics-grid">
        <!-- Gráfico de conversaciones -->
        <div class="ai-chatbot-analytics-card ai-chatbot-analytics-wide">
            <h3><?php echo esc_html__('Conversaciones por Día', 'ai-chatbot-uniformes'); ?></h3>
            <div class="ai-chatbot-chart-container">
                <canvas id="conversationsChart"></canvas>
            </div>
        </div>
        
        <!-- Consultas Frecuentes -->
        <div class="ai-chatbot-analytics-card">
            <h3><?php echo esc_html__('Consultas Frecuentes', 'ai-chatbot-uniformes'); ?></h3>
            
            <?php if (empty($frequent_queries)): ?>
                <p class="ai-chatbot-no-data"><?php echo esc_html__('No hay datos disponibles para el período seleccionado.', 'ai-chatbot-uniformes'); ?></p>
            <?php else: ?>
                <table class="ai-chatbot-analytics-table">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Consulta', 'ai-chatbot-uniformes'); ?></th>
                            <th><?php echo esc_html__('Frecuencia', 'ai-chatbot-uniformes'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($frequent_queries as $query): ?>
                            <tr>
                                <td title="<?php echo esc_attr($query['message']); ?>">
                                    <?php echo esc_html(mb_strimwidth($query['message'], 0, 50, '...')); ?>
                                </td>
                                <td><?php echo esc_html($query['frequency']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Temas Populares (Premium) -->
        <div class="ai-chatbot-analytics-card <?php echo !$has_advanced_analytics ? 'ai-chatbot-premium-feature' : ''; ?>">
            <h3>
                <?php echo esc_html__('Temas Populares', 'ai-chatbot-uniformes'); ?>
                <?php if (!$has_advanced_analytics): ?>
                    <span class="ai-chatbot-premium-tag"><?php echo esc_html__('Premium', 'ai-chatbot-uniformes'); ?></span>
                <?php endif; ?>
            </h3>
            
            <?php if (!$has_advanced_analytics): ?>
                <div class="ai-chatbot-premium-overlay">
                    <div class="ai-chatbot-premium-message">
                        <span class="dashicons dashicons-lock"></span>
                        <p><?php echo esc_html__('Función disponible en el Plan Premium', 'ai-chatbot-uniformes'); ?></p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=ai-chatbot-licensing')); ?>" class="button button-primary">
                            <?php echo esc_html__('Actualizar Ahora', 'ai-chatbot-uniformes'); ?>
                        </a>
                    </div>
                </div>
                <div class="ai-chatbot-blurred-content">
                    <!-- Contenido simulado para mostrar en versión borrosa -->
                    <div class="ai-chatbot-chart-container">
                        <canvas id="topicsChartDemo"></canvas>
                    </div>
                </div>
            <?php else: ?>
                <?php if (empty($popular_topics)): ?>
                    <p class="ai-chatbot-no-data"><?php echo esc_html__('No hay datos disponibles para el período seleccionado.', 'ai-chatbot-uniformes'); ?></p>
                <?php else: ?>
                    <div class="ai-chatbot-chart-container">
                        <canvas id="topicsChart"></canvas>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Últimas Conversaciones (Premium) -->
        <div class="ai-chatbot-analytics-card ai-chatbot-analytics-wide <?php echo !$has_advanced_analytics ? 'ai-chatbot-premium-feature' : ''; ?>">
            <h3>
                <?php echo esc_html__('Últimas Conversaciones', 'ai-chatbot-uniformes'); ?>
                <?php if (!$has_advanced_analytics): ?>
                    <span class="ai-chatbot-premium-tag"><?php echo esc_html__('Premium', 'ai-chatbot-uniformes'); ?></span>
                <?php endif; ?>
            </h3>
            
            <?php if (!$has_advanced_analytics): ?>
                <div class="ai-chatbot-premium-overlay">
                    <div class="ai-chatbot-premium-message">
                        <span class="dashicons dashicons-lock"></span>
                        <p><?php echo esc_html__('Función disponible en el Plan Premium', 'ai-chatbot-uniformes'); ?></p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=ai-chatbot-licensing')); ?>" class="button button-primary">
                            <?php echo esc_html__('Actualizar Ahora', 'ai-chatbot-uniformes'); ?>
                        </a>
                    </div>
                </div>
                <div class="ai-chatbot-blurred-content">
                    <!-- Contenido simulado para mostrar en versión borrosa -->
                    <table class="ai-chatbot-analytics-table">
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('ID', 'ai-chatbot-uniformes'); ?></th>
                                <th><?php echo esc_html__('Fecha', 'ai-chatbot-uniformes'); ?></th>
                                <th><?php echo esc_html__('Mensajes', 'ai-chatbot-uniformes'); ?></th>
                                <th><?php echo esc_html__('Primera Consulta', 'ai-chatbot-uniformes'); ?></th>
                                <th><?php echo esc_html__('Acciones', 'ai-chatbot-uniformes'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>1001</td><td>2023-08-15</td><td>8</td><td>Lorem ipsum dolor sit amet...</td><td><a href="#">Ver</a></td></tr>
                            <tr><td>1002</td><td>2023-08-15</td><td>5</td><td>Lorem ipsum dolor sit amet...</td><td><a href="#">Ver</a></td></tr>
                            <tr><td>1003</td><td>2023-08-14</td><td>12</td><td>Lorem ipsum dolor sit amet...</td><td><a href="#">Ver</a></td></tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <?php
                // Obtener últimas conversaciones (solo en premium)
                global $wpdb;
                $conversations_table = $wpdb->prefix . 'ai_chatbot_conversations';
                $messages_table = $wpdb->prefix . 'ai_chatbot_messages';
                
                $conversations = $wpdb->get_results(
                    "SELECT c.id, c.session_id, c.started_at, COUNT(m.id) as message_count,
                    (SELECT message FROM {$messages_table} WHERE conversation_id = c.id AND message_type = 'user' ORDER BY created_at ASC LIMIT 1) as first_message
                    FROM {$conversations_table} c
                    LEFT JOIN {$messages_table} m ON c.id = m.conversation_id
                    GROUP BY c.id
                    ORDER BY c.started_at DESC
                    LIMIT 10"
                );
                ?>
                
                <?php if (empty($conversations)): ?>
                    <p class="ai-chatbot-no-data"><?php echo esc_html__('No hay conversaciones disponibles.', 'ai-chatbot-uniformes'); ?></p>
                <?php else: ?>
                    <table class="ai-chatbot-analytics-table">
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('ID', 'ai-chatbot-uniformes'); ?></th>
                                <th><?php echo esc_html__('Fecha', 'ai-chatbot-uniformes'); ?></th>
                                <th><?php echo esc_html__('Mensajes', 'ai-chatbot-uniformes'); ?></th>
                                <th><?php echo esc_html__('Primera Consulta', 'ai-chatbot-uniformes'); ?></th>
                                <th><?php echo esc_html__('Acciones', 'ai-chatbot-uniformes'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($conversations as $conversation): ?>
                                <tr>
                                    <td><?php echo esc_html($conversation->id); ?></td>
                                    <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($conversation->started_at))); ?></td>
                                    <td><?php echo esc_html($conversation->message_count); ?></td>
                                    <td title="<?php echo esc_attr($conversation->first_message); ?>">
                                        <?php echo esc_html(mb_strimwidth($conversation->first_message, 0, 50, '...')); ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo esc_url(add_query_arg([
                                            'page' => 'ai-chatbot-conversation',
                                            'id' => $conversation->id,
                                        ], admin_url('admin.php'))); ?>" class="button button-small">
                                            <?php echo esc_html__('Ver', 'ai-chatbot-uniformes'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Datos para el gráfico de conversaciones
        const conversationsData = {
            labels: [
                <?php 
                $labels = [];
                foreach (array_keys($conversations_by_day) as $date) {
                    $labels[] = "'" . date_i18n(get_option('date_format'), strtotime($date)) . "'";
                }
                echo implode(', ', $labels);
                ?>
            ],
            datasets: [{
                label: '<?php echo esc_js(__('Conversaciones', 'ai-chatbot-uniformes')); ?>',
                data: [<?php echo implode(', ', array_values($conversations_by_day)); ?>],
                backgroundColor: 'rgba(63, 81, 181, 0.2)',
                borderColor: 'rgba(63, 81, 181, 1)',
                borderWidth: 2,
                tension: 0.4
            }]
        };

        // Configuración del gráfico de conversaciones
        const conversationsConfig = {
            type: 'line',
            data: conversationsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        };

        // Crear gráfico de conversaciones
        const conversationsChart = new Chart(
            document.getElementById('conversationsChart'),
            conversationsConfig
        );

        <?php if ($has_advanced_analytics && !empty($popular_topics)): ?>
        // Datos para el gráfico de temas populares
        const topicsData = {
            labels: [
                <?php 
                $topic_labels = [];
                foreach ($popular_topics as $topic) {
                    $topic_labels[] = "'" . esc_js($topic['word']) . "'";
                }
                echo implode(', ', $topic_labels);
                ?>
            ],
            datasets: [{
                data: [
                    <?php 
                    $topic_values = [];
                    foreach ($popular_topics as $topic) {
                        $topic_values[] = $topic['frequency'];
                    }
                    echo implode(', ', $topic_values);
                    ?>
                ],
                backgroundColor: [
                    'rgba(63, 81, 181, 0.7)',
                    'rgba(33, 150, 243, 0.7)',
                    'rgba(0, 188, 212, 0.7)',
                    'rgba(76, 175, 80, 0.7)',
                    'rgba(139, 195, 74, 0.7)',
                    'rgba(205, 220, 57, 0.7)',
                    'rgba(255, 235, 59, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(255, 152, 0, 0.7)',
                    'rgba(255, 87, 34, 0.7)'
                ],
                borderWidth: 1
            }]
        };

        // Configuración del gráfico de temas populares
        const topicsConfig = {
            type: 'pie',
            data: topicsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        };

        // Crear gráfico de temas populares
        const topicsChart = new Chart(
            document.getElementById('topicsChart'),
            topicsConfig
        );
        <?php endif; ?>

        <?php if (!$has_advanced_analytics): ?>
        // Datos simulados para demo (versión borrosa)
        const demoTopicsData = {
            labels: ['Tema 1', 'Tema 2', 'Tema 3', 'Tema 4', 'Tema 5'],
            datasets: [{
                data: [12, 19, 8, 5, 2],
                backgroundColor: [
                    'rgba(63, 81, 181, 0.7)',
                    'rgba(33, 150, 243, 0.7)',
                    'rgba(0, 188, 212, 0.7)',
                    'rgba(76, 175, 80, 0.7)',
                    'rgba(139, 195, 74, 0.7)'
                ],
                borderWidth: 1
            }]
        };

        // Configuración del gráfico demo
        const demoTopicsConfig = {
            type: 'pie',
            data: demoTopicsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        };

        // Crear gráfico demo
        const demoTopicsChart = new Chart(
            document.getElementById('topicsChartDemo'),
            demoTopicsConfig
        );
        <?php endif; ?>
    });
</script>

<style>
    /* Estilos para la página de analíticas */
    .ai-chatbot-analytics-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .ai-chatbot-premium-notice {
        background-color: #f0f6fc;
        border-left: 4px solid #2271b1;
        padding: 10px 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    
    .ai-chatbot-premium-notice .dashicons {
        margin-right: 10px;
        color: #2271b1;
    }
    
    .ai-chatbot-analytics-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .ai-chatbot-metric-card {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
    }
    
    .ai-chatbot-metric-icon {
        background-color: #f0f6fc;
        color: #2271b1;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    
    .ai-chatbot-metric-icon .dashicons {
        font-size: 24px;
        width: 24px;
        height: 24px;
    }
    
    .ai-chatbot-metric-value {
        font-size: 24px;
        font-weight: bold;
        color: #23282d;
        line-height: 1.2;
    }
    
    .ai-chatbot-metric-label {
        color: #646970;
        font-size: 13px;
    }
    
    .ai-chatbot-analytics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .ai-chatbot-analytics-wide {
        grid-column: span 2;
    }
    
    .ai-chatbot-analytics-card {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }
    
    .ai-chatbot-chart-container {
        height: 300px;
        position: relative;
    }
    
    .ai-chatbot-analytics-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .ai-chatbot-analytics-table th,
    .ai-chatbot-analytics-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .ai-chatbot-analytics-table th {
        font-weight: 600;
        color: #23282d;
    }
    
    .ai-chatbot-no-data {
        padding: 30px 0;
        text-align: center;
        color: #646970;
        font-style: italic;
    }
    
    /* Estilos para características premium */
    .ai-chatbot-premium-feature {
        position: relative;
    }
    
    .ai-chatbot-premium-tag {
        background-color: #ff9800;
        color: white;
        font-size: 11px;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 3px;
        margin-left: 5px;
        vertical-align: middle;
    }
    
    .ai-chatbot-premium-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        backdrop-filter: blur(2px);
    }
    
    .ai-chatbot-premium-message {
        text-align: center;
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        max-width: 80%;
    }
    
    .ai-chatbot-premium-message .dashicons {
        font-size: 30px;
        width: 30px;
        height: 30px;
        color: #ff9800;
        margin-bottom: 10px;
    }
    
    .ai-chatbot-blurred-content {
        filter: blur(3px);
        pointer-events: none;
    }
    
    /* Responsive */
    @media (max-width: 782px) {
        .ai-chatbot-analytics-grid {
            grid-template-columns: 1fr;
        }
        
        .ai-chatbot-analytics-wide {
            grid-column: span 1;
        }
    }
</style>