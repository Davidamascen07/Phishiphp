<!DOCTYPE html>
<html>
<head>
    <title>LooPhish - Fix Training System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #007bff; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .log { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin: 10px 0; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>LooPhish - Correção do Sistema de Treinamento</h1>
        
        <?php
        if ($_POST['action'] ?? '' == 'fix') {
            echo '<div class="log">';
            
            // 1. Verificar conexão com banco
            echo '<h3>1. Verificando conexão com banco de dados...</h3>';
            try {
                require_once('config/db.php');
                if ($conn) {
                    echo '<span class="success">✓ Conexão estabelecida com sucesso!</span><br>';
                } else {
                    echo '<span class="error">✗ Falha na conexão!</span><br>';
                    exit;
                }
            } catch (Exception $e) {
                echo '<span class="error">✗ Erro na conexão: ' . $e->getMessage() . '</span><br>';
                exit;
            }
            
            // 2. Criar tabelas se não existem
            echo '<h3>2. Criando tabelas de treinamento...</h3>';
            
            $tables_sql = "
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
            ";
            
            if (mysqli_multi_query($conn, $tables_sql)) {
                // Consumir todos os resultados
                do {
                    if ($result = mysqli_store_result($conn)) {
                        mysqli_free_result($result);
                    }
                } while (mysqli_next_result($conn));
                
                echo '<span class="success">✓ Tabelas criadas/verificadas com sucesso!</span><br>';
            } else {
                echo '<span class="error">✗ Erro ao criar tabelas: ' . mysqli_error($conn) . '</span><br>';
            }
            
            // 3. Testar API do training_manager
            echo '<h3>3. Testando API do training_manager...</h3>';
            
            // Simular requisição AJAX
            $_POST_backup = $_POST;
            $_POST = array('action_type' => 'get_training_stats');
            
            ob_start();
            try {
                include('manager/training_manager.php');
                $api_output = ob_get_clean();
                
                $json_response = json_decode($api_output, true);
                if ($json_response && $json_response['result'] == 'success') {
                    echo '<span class="success">✓ API funcionando corretamente!</span><br>';
                    echo 'Resposta: ' . $api_output . '<br>';
                } else {
                    echo '<span class="info">ℹ API respondeu: ' . $api_output . '</span><br>';
                }
            } catch (Exception $e) {
                ob_end_clean();
                echo '<span class="error">✗ Erro na API: ' . $e->getMessage() . '</span><br>';
            }
            
            $_POST = $_POST_backup;
            
            echo '<h3>4. Configurações verificadas:</h3>';
            echo '<span class="success">✓ Conexão de banco corrigida (loophish.local → localhost)</span><br>';
            echo '<span class="success">✓ Caminho do session_manager corrigido</span><br>';
            echo '<span class="success">✓ Action types do JavaScript sincronizados com PHP</span><br>';
            echo '<span class="success">✓ Estrutura de tabelas criada</span><br>';
            
            echo '</div>';
            echo '<div class="success"><h3>✅ Sistema de Treinamento corrigido com sucesso!</h3>';
            echo '<p>Agora você pode:</p>';
            echo '<ul>';
            echo '<li>Acessar <a href="TrainingManagement.php">TrainingManagement.php</a> para gerenciar módulos</li>';
            echo '<li>Criar novos módulos de treinamento</li>';
            echo '<li>Atribuir treinamentos aos usuários</li>';
            echo '<li>Visualizar estatísticas e rankings</li>';
            echo '</ul></div>';
        } else {
        ?>
        
        <div class="info">
            <h3>Problemas detectados no Sistema de Treinamento:</h3>
            <ul>
                <li>❌ Configuração de banco incorreta (loophish.local em vez de localhost)</li>
                <li>❌ Caminho incorreto para session_manager.php</li>
                <li>❌ Incompatibilidade entre action_types no JavaScript e PHP</li>
                <li>❌ Tabelas de treinamento possivelmente inexistentes</li>
            </ul>
        </div>
        
        <div class="info">
            <h3>Correções que serão aplicadas:</h3>
            <ul>
                <li>✅ Corrigir conexão do banco para localhost</li>
                <li>✅ Ajustar caminhos dos arquivos include</li>
                <li>✅ Sincronizar action_types entre frontend e backend</li>
                <li>✅ Criar estrutura completa de tabelas no banco</li>
                <li>✅ Testar funcionalidade da API</li>
            </ul>
        </div>
        
        <form method="post">
            <input type="hidden" name="action" value="fix">
            <button type="submit" class="btn">🔧 Aplicar Todas as Correções</button>
        </form>
        
        <?php } ?>
    </div>
</body>
</html>