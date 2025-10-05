<?php
/**
 * EXECUTOR DE SCRIPT SQL - FASE 2
 * Execute este arquivo via navegador para aplicar as mudanças do banco de dados
 */

require_once(dirname(__FILE__) . '/spear/config/db.php');

// Execute o SQL da Fase 2
$sql_statements = [
    // ETAPA 1: Adicionar campos de treinamento em tb_core_web_tracker_list
    "ALTER TABLE `tb_core_web_tracker_list` ADD COLUMN `training_enabled` tinyint(1) NOT NULL DEFAULT 0",
    "ALTER TABLE `tb_core_web_tracker_list` ADD COLUMN `training_module_id` varchar(50) DEFAULT NULL", 
    "ALTER TABLE `tb_core_web_tracker_list` ADD COLUMN `training_trigger_condition` enum('immediate','on_completion','on_failure','on_interaction') DEFAULT 'immediate'",
    "ALTER TABLE `tb_core_web_tracker_list` ADD COLUMN `training_completion_redirect` varchar(255) DEFAULT NULL",
    
    // ETAPA 2: Adicionar campos de treinamento em tb_core_mailcamp_list
    "ALTER TABLE `tb_core_mailcamp_list` ADD COLUMN `training_enabled` tinyint(1) NOT NULL DEFAULT 0",
    "ALTER TABLE `tb_core_mailcamp_list` ADD COLUMN `training_module_id` varchar(50) DEFAULT NULL",
    "ALTER TABLE `tb_core_mailcamp_list` ADD COLUMN `training_target_percentage` int(3) DEFAULT 100",
    "ALTER TABLE `tb_core_mailcamp_list` ADD COLUMN `training_notification_emails` text DEFAULT NULL",
    
    // ETAPA 3: Criar tabela de relacionamento campanha-treinamento
    "CREATE TABLE IF NOT EXISTS `tb_campaign_training_results` (
        `result_id` varchar(50) NOT NULL PRIMARY KEY,
        `client_id` varchar(50) NOT NULL,
        `campaign_id` varchar(50) DEFAULT NULL,
        `tracker_id` varchar(50) DEFAULT NULL,
        `training_module_id` varchar(50) NOT NULL,
        `participant_email` varchar(255) NOT NULL,
        `participant_name` varchar(255) DEFAULT NULL,
        `training_started_at` datetime DEFAULT NULL,
        `training_completed_at` datetime DEFAULT NULL,
        `training_score` int(3) DEFAULT NULL,
        `training_status` enum('not_started','in_progress','completed','failed') DEFAULT 'not_started',
        `questions_answered` int(5) DEFAULT 0,
        `correct_answers` int(5) DEFAULT 0,
        `completion_percentage` decimal(5,2) DEFAULT 0.00,
        `time_spent_minutes` int(10) DEFAULT 0,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
    
    // ETAPA 4: Criar índices
    "CREATE INDEX `idx_campaign_training_client` ON `tb_campaign_training_results` (`client_id`)",
    "CREATE INDEX `idx_campaign_training_campaign` ON `tb_campaign_training_results` (`campaign_id`)",
    "CREATE INDEX `idx_campaign_training_tracker` ON `tb_campaign_training_results` (`tracker_id`)",
    "CREATE INDEX `idx_campaign_training_module` ON `tb_campaign_training_results` (`training_module_id`)",
    "CREATE INDEX `idx_campaign_training_email` ON `tb_campaign_training_results` (`participant_email`)",
    "CREATE INDEX `idx_campaign_training_status` ON `tb_campaign_training_results` (`training_status`)",
    "CREATE INDEX `idx_campaign_training_enabled` ON `tb_core_mailcamp_list` (`client_id`, `training_enabled`, `camp_status`)",
    "CREATE INDEX `idx_tracker_training_enabled` ON `tb_core_web_tracker_list` (`client_id`, `training_enabled`, `active`)"
];

echo "<h1>🎯 LOOPHISH V2.0 - FASE 2: Execução de Script SQL</h1>";
echo "<p>Executando modificações no banco de dados...</p>";

$success_count = 0;
$error_count = 0;

foreach ($sql_statements as $index => $sql) {
    echo "<hr>";
    echo "<h3>Comando " . ($index + 1) . ":</h3>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    
    if (mysqli_query($conn, $sql)) {
        echo "<p style='color: green;'>✅ <strong>Sucesso!</strong></p>";
        $success_count++;
    } else {
        $error = mysqli_error($conn);
        if (strpos($error, 'Duplicate column name') !== false || 
            strpos($error, 'already exists') !== false ||
            strpos($error, 'Duplicate key name') !== false) {
            echo "<p style='color: orange;'>⚠️ <strong>Já existe:</strong> " . htmlspecialchars($error) . "</p>";
            $success_count++;
        } else {
            echo "<p style='color: red;'>❌ <strong>Erro:</strong> " . htmlspecialchars($error) . "</p>";
            $error_count++;
        }
    }
}

echo "<hr>";
echo "<h2>📊 Resumo da Execução:</h2>";
echo "<p><strong>Comandos executados com sucesso:</strong> " . $success_count . "</p>";
echo "<p><strong>Comandos com erro:</strong> " . $error_count . "</p>";

if ($error_count == 0) {
    echo "<h2 style='color: green;'>🎉 FASE 2 - Integração Campanha-Treinamento configurada com sucesso!</h2>";
    echo "<ul>";
    echo "<li>✅ Campos de treinamento adicionados em tb_core_web_tracker_list</li>";
    echo "<li>✅ Campos de treinamento adicionados em tb_core_mailcamp_list</li>";
    echo "<li>✅ Tabela tb_campaign_training_results criada</li>";
    echo "<li>✅ Índices de performance criados</li>";
    echo "</ul>";
    echo "<p><a href='TrackerGenerator.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Testar TrackerGenerator</a></p>";
} else {
    echo "<h2 style='color: red;'>⚠️ Alguns comandos falharam. Verifique os erros acima.</h2>";
}

mysqli_close($conn);
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
hr { margin: 20px 0; }
</style>