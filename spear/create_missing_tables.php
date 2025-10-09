<?php
/**
 * Script para criar as tabelas que estão faltando no sistema de treinamento
 */

require_once 'config/db.php';

echo "<h2>🔧 Criando Tabelas Ausentes</h2>\n";

// SQL para criar tb_training_analytics
$sql_analytics = "
CREATE TABLE IF NOT EXISTS `tb_training_analytics` (
  `analytics_id` varchar(50) NOT NULL PRIMARY KEY,
  `client_id` varchar(50) NOT NULL,
  `module_id` varchar(50) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `event_type` enum('started','progress','completed','certificate_issued') NOT NULL,
  `event_data` json DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_client_module` (`client_id`, `module_id`),
  INDEX `idx_user_email` (`user_email`),
  INDEX `idx_event_type` (`event_type`),
  INDEX `idx_created_date` (`created_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

// SQL para criar tb_training_redirect_logs
$sql_redirect_logs = "
CREATE TABLE IF NOT EXISTS `tb_training_redirect_logs` (
  `log_id` varchar(50) NOT NULL PRIMARY KEY,
  `client_id` varchar(50) NOT NULL,
  `rid` varchar(50) NOT NULL,
  `campaign_id` varchar(50) DEFAULT NULL,
  `tracker_id` varchar(50) DEFAULT NULL,
  `module_id` varchar(50) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `redirect_type` enum('email_click','tracker_visit','manual') NOT NULL,
  `source_url` text DEFAULT NULL,
  `target_url` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `redirect_status` enum('successful','failed','blocked') DEFAULT 'successful',
  `error_message` text DEFAULT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_client_rid` (`client_id`, `rid`),
  INDEX `idx_campaign` (`campaign_id`),
  INDEX `idx_tracker` (`tracker_id`),
  INDEX `idx_module` (`module_id`),
  INDEX `idx_user_email` (`user_email`),
  INDEX `idx_redirect_type` (`redirect_type`),
  INDEX `idx_created_date` (`created_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

echo "<h3>Criando tb_training_analytics...</h3>\n";
if (mysqli_query($conn, $sql_analytics)) {
    echo "✅ <strong>tb_training_analytics</strong> criada com sucesso!<br>\n";
} else {
    echo "❌ Erro ao criar tb_training_analytics: " . mysqli_error($conn) . "<br>\n";
}

echo "<h3>Criando tb_training_redirect_logs...</h3>\n";
if (mysqli_query($conn, $sql_redirect_logs)) {
    echo "✅ <strong>tb_training_redirect_logs</strong> criada com sucesso!<br>\n";
} else {
    echo "❌ Erro ao criar tb_training_redirect_logs: " . mysqli_error($conn) . "<br>\n";
}

echo "<br><h3>🎉 Processo concluído!</h3>\n";
echo "<p><a href='test_training_system.php'>🔍 Executar verificação novamente</a></p>\n";
echo "<p><a href='TrainingManagement.php'>📚 Ir para o Sistema de Treinamento</a></p>\n";

mysqli_close($conn);
?>