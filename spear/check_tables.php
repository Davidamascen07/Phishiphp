<?php
require_once('config/db.php');

$tables = [
    'tb_training_modules', 
    'tb_training_assignments', 
    'tb_training_progress', 
    'tb_training_quiz_results', 
    'tb_training_questions', 
    'tb_training_certificates',
    'tb_training_rankings'
];

echo "Verificando tabelas de treinamento:\n\n";

foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "✓ Tabela $table existe\n";
    } else {
        echo "✗ Tabela $table NÃO existe\n";
    }
}

// Se as tabelas não existem, vamos criá-las
$missing_tables = [];
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) == 0) {
        $missing_tables[] = $table;
    }
}

if (!empty($missing_tables)) {
    echo "\nCriando tabelas faltantes...\n";
    
    // SQL para criar as tabelas
    $create_sql = "
    CREATE TABLE IF NOT EXISTS `tb_training_modules` (
        `module_id` varchar(50) NOT NULL PRIMARY KEY,
        `module_name` varchar(200) NOT NULL,
        `module_description` text DEFAULT NULL,
        `module_type` varchar(50) NOT NULL,
        `content_data` longtext DEFAULT NULL,
        `quiz_data` longtext DEFAULT NULL,
        `quiz_enabled` tinyint(1) DEFAULT 0,
        `passing_score` int(11) DEFAULT 70,
        `estimated_duration` int(11) DEFAULT 15,
        `difficulty_level` varchar(20) DEFAULT 'basic',
        `category` varchar(100) DEFAULT NULL,
        `tags` text DEFAULT NULL,
        `status` tinyint(1) DEFAULT 1,
        `created_by` varchar(50) DEFAULT NULL,
        `created_date` varchar(50) NOT NULL,
        `modified_date` varchar(50) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_assignments` (
        `assignment_id` varchar(50) NOT NULL PRIMARY KEY,
        `module_id` varchar(50) NOT NULL,
        `client_id` varchar(50) DEFAULT NULL,
        `campaign_id` varchar(50) DEFAULT NULL,
        `assigned_users` longtext DEFAULT NULL,
        `assigned_departments` text DEFAULT NULL,
        `assignment_type` varchar(50) DEFAULT 'manual',
        `start_date` varchar(50) DEFAULT NULL,
        `end_date` varchar(50) DEFAULT NULL,
        `auto_assign_new_users` tinyint(1) DEFAULT 0,
        `send_reminders` tinyint(1) DEFAULT 1,
        `reminder_interval` int(11) DEFAULT 7,
        `created_date` varchar(50) NOT NULL,
        `status` tinyint(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_progress` (
        `progress_id` varchar(50) NOT NULL PRIMARY KEY,
        `assignment_id` varchar(50) NOT NULL,
        `user_email` varchar(100) NOT NULL,
        `user_name` varchar(100) DEFAULT NULL,
        `client_id` varchar(50) DEFAULT NULL,
        `module_id` varchar(50) NOT NULL,
        `status` varchar(30) DEFAULT 'not_started',
        `progress_percentage` int(11) DEFAULT 0,
        `time_spent` int(11) DEFAULT 0,
        `quiz_score` int(11) DEFAULT 0,
        `quiz_attempts` int(11) DEFAULT 0,
        `completion_time` varchar(50) DEFAULT NULL,
        `certificate_issued` tinyint(1) DEFAULT 0,
        `certificate_id` varchar(50) DEFAULT NULL,
        `last_activity` varchar(50) DEFAULT NULL,
        `created_date` varchar(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_rankings` (
        `ranking_id` varchar(50) NOT NULL PRIMARY KEY,
        `user_email` varchar(100) NOT NULL,
        `user_name` varchar(100) DEFAULT NULL,
        `client_id` varchar(50) DEFAULT NULL,
        `total_score` int(11) DEFAULT 0,
        `completed_modules` int(11) DEFAULT 0,
        `certificates_earned` int(11) DEFAULT 0,
        `avg_quiz_score` decimal(5,2) DEFAULT 0.00,
        `total_time_spent` int(11) DEFAULT 0,
        `last_activity` varchar(50) DEFAULT NULL,
        `rank_position` int(11) DEFAULT 0,
        `points_earned` int(11) DEFAULT 0,
        `badges_earned` text DEFAULT NULL,
        `updated_date` varchar(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_quiz_results` (
        `result_id` varchar(50) NOT NULL PRIMARY KEY,
        `progress_id` varchar(50) NOT NULL,
        `user_email` varchar(100) NOT NULL,
        `module_id` varchar(50) NOT NULL,
        `attempt_number` int(11) NOT NULL,
        `questions_data` longtext NOT NULL,
        `answers_data` longtext NOT NULL,
        `score` int(11) NOT NULL,
        `total_questions` int(11) NOT NULL,
        `correct_answers` int(11) NOT NULL,
        `time_taken` int(11) DEFAULT 0,
        `submitted_date` varchar(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_questions` (
        `question_id` varchar(50) NOT NULL PRIMARY KEY,
        `question_text` text NOT NULL,
        `question_type` varchar(50) NOT NULL,
        `category` varchar(100) DEFAULT NULL,
        `difficulty` varchar(20) DEFAULT 'basic',
        `choices` longtext DEFAULT NULL,
        `correct_answer` varchar(255) DEFAULT NULL,
        `supporting_html` longtext DEFAULT NULL,
        `explanation` text DEFAULT NULL,
        `created_by` varchar(50) DEFAULT NULL,
        `created_date` varchar(50) NOT NULL,
        `status` tinyint(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_certificates` (
        `certificate_id` varchar(50) NOT NULL PRIMARY KEY,
        `progress_id` varchar(50) NOT NULL,
        `user_email` varchar(100) NOT NULL,
        `user_name` varchar(100) DEFAULT NULL,
        `client_id` varchar(50) DEFAULT NULL,
        `module_id` varchar(50) NOT NULL,
        `module_name` varchar(200) NOT NULL,
        `score_achieved` int(11) NOT NULL,
        `completion_date` varchar(50) NOT NULL,
        `validation_code` varchar(50) NOT NULL UNIQUE,
        `issued_date` varchar(50) NOT NULL,
        `template_data` longtext DEFAULT NULL,
        `status` tinyint(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    if (mysqli_multi_query($conn, $create_sql)) {
        // Consume todos os resultados
        do {
            if ($result = mysqli_store_result($conn)) {
                mysqli_free_result($result);
            }
        } while (mysqli_next_result($conn));
        
        echo "✓ Tabelas criadas com sucesso!\n";
    } else {
        echo "✗ Erro ao criar tabelas: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "\n✓ Todas as tabelas já existem!\n";
}

mysqli_close($conn);
?>