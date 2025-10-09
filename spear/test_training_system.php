<?php
/**
 * Script para verificar e validar todas as tabelas do sistema de treinamento
 */

require_once 'manager/common_functions.php';
require_once 'config/db.php';

echo "<h2>🔍 Verificação do Sistema de Treinamento</h2>\n";

// Lista das tabelas necessárias
$training_tables = [
    'tb_training_modules' => 'Módulos de treinamento',
    'tb_training_assignments' => 'Atribuições de treinamento',
    'tb_training_progress' => 'Progresso do usuário',
    'tb_training_certificates' => 'Certificados emitidos',
    'tb_training_quiz_results' => 'Resultados dos quizzes',
    'tb_training_questions' => 'Perguntas dos quizzes',
    'tb_training_rankings' => 'Rankings de usuários',
    'tb_campaign_training_association' => 'Associação campanhas-treinamento',
    'tb_training_analytics' => 'Analytics de treinamento',
    'tb_training_redirect_logs' => 'Logs de redirecionamento'
];

$existing_tables = [];
$missing_tables = [];

echo "<h3>📋 Status das Tabelas:</h3>\n";

foreach ($training_tables as $table => $description) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "✅ <strong>$table</strong> - $description (EXISTS)<br>\n";
        $existing_tables[] = $table;
        
        // Verificar estrutura da tabela
        $structure = mysqli_query($conn, "DESCRIBE $table");
        if ($structure) {
            $column_count = mysqli_num_rows($structure);
            echo "&nbsp;&nbsp;&nbsp;&nbsp;📊 Colunas: $column_count<br>\n";
        }
        
        // Verificar se tem dados
        $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM $table");
        if ($count_result) {
            $count = mysqli_fetch_assoc($count_result)['total'];
            echo "&nbsp;&nbsp;&nbsp;&nbsp;📈 Registros: $count<br>\n";
        }
        
    } else {
        echo "❌ <strong>$table</strong> - $description (MISSING)<br>\n";
        $missing_tables[] = $table;
    }
    echo "<br>\n";
}

echo "<hr>\n";
echo "<h3>📊 Resumo:</h3>\n";
echo "✅ <strong>Tabelas Existentes:</strong> " . count($existing_tables) . "/" . count($training_tables) . "<br>\n";
echo "❌ <strong>Tabelas Ausentes:</strong> " . count($missing_tables) . "<br>\n";

if (count($missing_tables) > 0) {
    echo "<br><h4>⚠️ Tabelas que precisam ser criadas:</h4>\n";
    foreach ($missing_tables as $table) {
        echo "- $table<br>\n";
    }
}

// Testar se o TrainingManager está funcionando
echo "<hr>\n";
echo "<h3>🧪 Teste do TrainingManager:</h3>\n";

try {
    require_once 'manager/training_manager.php';
    $training_manager = new TrainingManager();
    echo "✅ <strong>TrainingManager</strong> - Classe instanciada com sucesso<br>\n";
    
    // Testar método getAllTrainingModules
    $modules = $training_manager->getAllTrainingModules();
    echo "✅ <strong>getAllTrainingModules()</strong> - Método funcionando (retornou " . count($modules) . " módulos)<br>\n";
    
    if (count($modules) > 0) {
        echo "<br><h4>📚 Módulos de Treinamento Existentes:</h4>\n";
        foreach ($modules as $module) {
            echo "- <strong>{$module['module_name']}</strong> (ID: {$module['module_id']}, Tipo: {$module['module_type']})<br>\n";
        }
    } else {
        echo "⚠️ <strong>Nenhum módulo encontrado</strong> - Você precisará criar módulos de teste<br>\n";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Erro no TrainingManager:</strong> " . $e->getMessage() . "<br>\n";
}

// Verificar integrações de campanha
echo "<hr>\n";
echo "<h3>🔗 Verificação de Integrações:</h3>\n";

// Verificar se as colunas de integração existem na tabela de campanhas
$email_campaign_integration = mysqli_query($conn, "SHOW COLUMNS FROM tb_core_mailcamp_list LIKE '%training%'");
if (mysqli_num_rows($email_campaign_integration) > 0) {
    echo "✅ <strong>Integração Email Campaigns</strong> - Colunas de integração encontradas<br>\n";
} else {
    echo "❌ <strong>Integração Email Campaigns</strong> - Colunas de integração ausentes<br>\n";
}

$web_tracker_integration = mysqli_query($conn, "SHOW COLUMNS FROM tb_core_web_tracker_list LIKE '%training%'");
if (mysqli_num_rows($web_tracker_integration) > 0) {
    echo "✅ <strong>Integração Web Tracker</strong> - Colunas de integração encontradas<br>\n";
} else {
    echo "❌ <strong>Integração Web Tracker</strong> - Colunas de integração ausentes<br>\n";
}

// Verificar arquivos essenciais
echo "<hr>\n";
echo "<h3>📁 Verificação de Arquivos:</h3>\n";

$essential_files = [
    'TrainingManagement.php' => 'Interface de gerenciamento',
    'training_player.php' => 'Player de treinamento',
    'training_quiz.php' => 'Sistema de quiz',
    'training_completion.php' => 'Página de conclusão',
    'training_redirect.php' => 'Redirecionamento de campanhas',
    'certificate_view.php' => 'Visualização de certificados',
    'manager/training_manager.php' => 'Gerenciador backend',
    'manager/certificate_manager.php' => 'Gerenciador de certificados',
    'manager/training_analytics.php' => 'Sistema de analytics'
];

foreach ($essential_files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ <strong>$file</strong> - $description<br>\n";
    } else {
        echo "❌ <strong>$file</strong> - $description (MISSING)<br>\n";
    }
}

echo "<hr>\n";
echo "<h3>🎯 Próximos Passos para Teste:</h3>\n";

if (count($existing_tables) >= 8 && file_exists('TrainingManagement.php')) {
    echo "✅ <strong>Sistema está pronto para teste!</strong><br>\n";
    echo "<br><strong>Recomendações:</strong><br>\n";
    echo "1. 📚 Acesse <a href='TrainingManagement.php'>TrainingManagement.php</a> para criar um módulo<br>\n";
    echo "2. 🎬 Crie um módulo misto (vídeo + quiz) para teste completo<br>\n";
    echo "3. 📧 Configure uma campanha de email que redirecione para treinamento<br>\n";
    echo "4. 🧪 Teste o fluxo completo: email → clique → treinamento → certificado<br>\n";
} else {
    echo "⚠️ <strong>Sistema precisa de configuração adicional</strong><br>\n";
    echo "<br><strong>Ações necessárias:</strong><br>\n";
    
    if (count($missing_tables) > 0) {
        echo "- Criar tabelas ausentes do banco de dados<br>\n";
    }
    
    if (!file_exists('TrainingManagement.php')) {
        echo "- Verificar arquivos de interface ausentes<br>\n";
    }
}

echo "<br><em>Verificação concluída em " . date('Y-m-d H:i:s') . "</em>\n";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3, h4 { color: #2c3e50; }
strong { color: #2980b9; }
hr { margin: 20px 0; border: none; border-top: 2px solid #ecf0f1; }
</style>