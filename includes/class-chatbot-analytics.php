<?php
/**
 * Clase para analíticas del chatbot
 *
 * @package AIChatbot
 */

namespace AIChatbot;

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase que maneja analíticas y estadísticas del chatbot
 */
class Chatbot_Analytics {

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
     * Obtiene el número total de conversaciones
     *
     * @param string $period Periodo de tiempo ('all', 'today', 'week', 'month')
     * @return int Número total de conversaciones
     */
    public function get_total_conversations($period = 'all') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ai_chatbot_conversations';
        
        $where_clause = $this->get_period_where_clause($period, 'started_at');
        
        $query = "SELECT COUNT(*) FROM {$table_name}";
        
        if (!empty($where_clause)) {
            $query .= " WHERE {$where_clause}";
        }
        
        return (int) $wpdb->get_var($query);
    }

    /**
     * Obtiene el número total de mensajes
     *
     * @param string $period Periodo de tiempo ('all', 'today', 'week', 'month')
     * @return int Número total de mensajes
     */
    public function get_total_messages($period = 'all') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ai_chatbot_messages';
        
        $where_clause = $this->get_period_where_clause($period, 'created_at');
        
        $query = "SELECT COUNT(*) FROM {$table_name}";
        
        if (!empty($where_clause)) {
            $query .= " WHERE {$where_clause}";
        }
        
        return (int) $wpdb->get_var($query);
    }

    /**
     * Obtiene las consultas más frecuentes
     *
     * @param int $limit Número de consultas a obtener
     * @param string $period Periodo de tiempo ('all', 'today', 'week', 'month')
     * @return array Lista de consultas frecuentes
     */
    public function get_frequent_queries($limit = 10, $period = 'month') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ai_chatbot_messages';
        
        $where_clause = $this->get_period_where_clause($period, 'created_at');
        $where_clause .= (!empty($where_clause) ? ' AND ' : '') . "message_type = 'user'";
        
        $query = "
            SELECT message, COUNT(*) as frequency
            FROM {$table_name}
            WHERE {$where_clause}
            GROUP BY message
            ORDER BY frequency DESC
            LIMIT %d
        ";
        
        $query = $wpdb->prepare($query, $limit);
        
        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * Obtiene el tiempo promedio de respuesta
     *
     * @param string $period Periodo de tiempo ('all', 'today', 'week', 'month')
     * @return float Tiempo promedio en segundos
     */
    public function get_average_response_time($period = 'month') {
        global $wpdb;
        
        $messages_table = $wpdb->prefix . 'ai_chatbot_messages';
        
        $where_clause = $this->get_period_where_clause($period, 'm1.created_at');
        
        $query = "
            SELECT AVG(TIMESTAMPDIFF(SECOND, m1.created_at, m2.created_at)) as avg_time
            FROM {$messages_table} m1
            JOIN {$messages_table} m2 ON m1.conversation_id = m2.conversation_id
            WHERE {$where_clause}
            AND m1.message_type = 'user'
            AND m2.message_type = 'bot'
            AND m2.id = (
                SELECT MIN(id) 
                FROM {$messages_table} 
                WHERE conversation_id = m1.conversation_id 
                AND message_type = 'bot' 
                AND id > m1.id
            )
        ";
        
        $result = $wpdb->get_var($query);
        
        return $result ? round((float) $result, 2) : 0;
    }

    /**
     * Obtiene el número de conversaciones por día
     *
     * @param int $days Número de días a obtener
     * @return array Conversaciones por día
     */
    public function get_conversations_by_day($days = 30) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ai_chatbot_conversations';
        
        $query = "
            SELECT 
                DATE(started_at) as date,
                COUNT(*) as count
            FROM {$table_name}
            WHERE started_at >= DATE_SUB(CURDATE(), INTERVAL %d DAY)
            GROUP BY DATE(started_at)
            ORDER BY date ASC
        ";
        
        $query = $wpdb->prepare($query, $days);
        
        $results = $wpdb->get_results($query, ARRAY_A);
        
        // Rellenar días sin datos
        $data = [];
        $end_date = current_time('Y-m-d');
        $start_date = date('Y-m-d', strtotime("-{$days} days"));
        
        $current = $start_date;
        while ($current <= $end_date) {
            $data[$current] = 0;
            $current = date('Y-m-d', strtotime($current . ' +1 day'));
        }
        
        foreach ($results as $row) {
            $data[$row['date']] = (int) $row['count'];
        }
        
        return $data;
    }

    /**
     * Obtiene los temas más discutidos basados en análisis de palabras clave
     *
     * @param int $limit Número de temas a obtener
     * @return array Lista de temas populares
     */
    public function get_popular_topics($limit = 10) {
        // Esta función solo está disponible en el plan premium
        if (!$this->licensing->is_feature_available('advanced_analytics')) {
            return [];
        }
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ai_chatbot_messages';
        
        // Lista de palabras a ignorar
        $stop_words = ['el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas', 'y', 'o', 'pero', 'si', 
                      'de', 'a', 'en', 'por', 'para', 'con', 'sin', 'sobre', 'entre', 'es', 'son',
                      'como', 'que', 'cuando', 'donde', 'quien', 'quienes', 'cuyo', 'cuya', 'cual',
                      'este', 'esta', 'estos', 'estas', 'ese', 'esa', 'esos', 'esas', 'aquel', 'aquella'];
        
        // Convertir lista de palabras a ignorar en expresión regular
        $stop_words_regex = implode('|', array_map('preg_quote', $stop_words));
        
        $query = "
            SELECT 
                LOWER(
                    SUBSTRING_INDEX(
                        SUBSTRING_INDEX(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(message, '.', ' '),
                                        ',', ' '
                                    ),
                                    '?', ' '
                                ),
                                '¿', ' '
                            ),
                            ' ', n.n
                        ),
                        ' ', -1
                    )
                ) as word,
                COUNT(*) as frequency
            FROM {$table_name}
            CROSS JOIN (
                SELECT a.N + b.N * 10 + 1 as n
                FROM 
                    (SELECT 0 as N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a,
                    (SELECT 0 as N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) b
                ORDER BY n
            ) n
            WHERE message_type = 'user'
            AND CHAR_LENGTH(
                SUBSTRING_INDEX(
                    SUBSTRING_INDEX(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(message, '.', ' '),
                                    ',', ' '
                                ),
                                '?', ' '
                            ),
                            '¿', ' '
                        ),
                        ' ', n.n
                    ),
                    ' ', -1
                )
            ) > 3
            AND SUBSTRING_INDEX(
                SUBSTRING_INDEX(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(message, '.', ' '),
                                ',', ' '
                            ),
                            '?', ' '
                        ),
                        '¿', ' '
                    ),
                    ' ', n.n
                ),
                ' ', -1
            ) NOT REGEXP '{$stop_words_regex}'
            GROUP BY word
            ORDER BY frequency DESC
            LIMIT %d
        ";
        
        $query = $wpdb->prepare($query, $limit);
        
        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * Obtiene la tasa de satisfacción (si está habilitada la retroalimentación)
     *
     * @param string $period Periodo de tiempo ('all', 'today', 'week', 'month')
     * @return array Datos de satisfacción
     */
    public function get_satisfaction_rate($period = 'month') {
        // Esta función solo está disponible en el plan premium
        if (!$this->licensing->is_feature_available('advanced_analytics')) {
            return [
                'positive' => 0,
                'negative' => 0,
                'rate' => 0,
            ];
        }
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ai_chatbot_feedback';
        
        // Verificar si la tabla existe
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");
        
        if (!$table_exists) {
            return [
                'positive' => 0,
                'negative' => 0,
                'rate' => 0,
            ];
        }
        
        $where_clause = $this->get_period_where_clause($period, 'created_at');
        
        $query = "
            SELECT 
                SUM(CASE WHEN rating = 'positive' THEN 1 ELSE 0 END) as positive,
                SUM(CASE WHEN rating = 'negative' THEN 1 ELSE 0 END) as negative,
                COUNT(*) as total
            FROM {$table_name}
            WHERE {$where_clause}
        ";
        
        $result = $wpdb->get_row($query, ARRAY_A);
        
        if (!$result || !$result['total']) {
            return [
                'positive' => 0,
                'negative' => 0,
                'rate' => 0,
            ];
        }
        
        $positive = (int) $result['positive'];
        $negative = (int) $result['negative'];
        $total = (int) $result['total'];
        
        return [
            'positive' => $positive,
            'negative' => $negative,
            'rate' => round(($positive / $total) * 100, 1),
        ];
    }

    /**
     * Genera la cláusula WHERE para filtrar por periodo
     *
     * @param string $period Periodo ('all', 'today', 'week', 'month')
     * @param string $date_column Nombre de la columna de fecha
     * @return string Cláusula WHERE
     */
    private function get_period_where_clause($period, $date_column) {
        switch ($period) {
            case 'today':
                return "DATE({$date_column}) = CURDATE()";
            case 'week':
                return "DATE({$date_column}) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            case 'month':
                return "DATE({$date_column}) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            case 'all':
            default:
                return "";
        }
    }

    /**
     * Exporta los datos de conversaciones a CSV
     *
     * @param string $period Periodo de tiempo ('all', 'today', 'week', 'month')
     * @return string Ruta al archivo CSV generado
     */
    public function export_conversations_to_csv($period = 'month') {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'ai_chatbot_conversations';
        $messages_table = $wpdb->prefix . 'ai_chatbot_messages';
        
        $where_clause = $this->get_period_where_clause($period, 'c.started_at');
        
        $query = "
            SELECT 
                c.id,
                c.session_id,
                c.started_at,
                c.updated_at,
                m.message_type,
                m.message,
                m.created_at as message_time
            FROM {$conversations_table} c
            JOIN {$messages_table} m ON c.id = m.conversation_id
            WHERE {$where_clause}
            ORDER BY c.id, m.created_at
        ";
        
        $results = $wpdb->get_results($query, ARRAY_A);
        
        if (empty($results)) {
            return false;
        }
        
        // Crear archivo temporal
        $upload_dir = wp_upload_dir();
        $file_name = 'chatbot_conversations_' . date('Y-m-d_H-i-s') . '.csv';
        $file_path = $upload_dir['basedir'] . '/' . $file_name;
        $file_url = $upload_dir['baseurl'] . '/' . $file_name;
        
        // Abrir archivo
        $file = fopen($file_path, 'w');
        
        // Escribir encabezados
        fputcsv($file, [
            'Conversation ID',
            'Session ID',
            'Started At',
            'Message Type',
            'Message',
            'Message Time'
        ]);
        
        // Escribir datos
        foreach ($results as $row) {
            fputcsv($file, [
                $row['id'],
                $row['session_id'],
                $row['started_at'],
                $row['message_type'],
                $row['message'],
                $row['message_time']
            ]);
        }
        
        // Cerrar archivo
        fclose($file);
        
        return $file_url;
    }
}