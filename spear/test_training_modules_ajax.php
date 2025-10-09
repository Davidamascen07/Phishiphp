<?php
/**
 * Teste direto para verificar carregamento de módulos de treinamento
 */

require_once 'config/db.php';
require_once 'manager/training_manager.php';

echo "<h2>🔍 Teste de Carregamento de Módulos</h2>\n";

try {
    // Teste direto da classe TrainingManager
    echo "<h3>1. Testando TrainingManager diretamente:</h3>\n";
    
    if (class_exists('TrainingManager')) {
        echo "✅ Classe TrainingManager encontrada<br>\n";
        
        $training_manager = new TrainingManager();
        echo "✅ TrainingManager instanciado com sucesso<br>\n";
        
        // Teste do método getAllTrainingModules
        $modules = $training_manager->getAllTrainingModules();
        
        if ($modules !== false) {
            echo "✅ Método getAllTrainingModules executado com sucesso<br>\n";
            echo "<strong>Total de módulos:</strong> " . count($modules) . "<br>\n";
            
            if (count($modules) > 0) {
                echo "<h4>Módulos encontrados:</h4>\n";
                echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
                echo "<tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Status</th></tr>\n";
                foreach ($modules as $module) {
                    echo "<tr>\n";
                    echo "<td>" . htmlspecialchars($module['module_id']) . "</td>\n";
                    echo "<td>" . htmlspecialchars($module['module_name']) . "</td>\n";
                    echo "<td>" . htmlspecialchars($module['module_type']) . "</td>\n";
                    echo "<td>" . ($module['status'] == 1 ? 'Ativo' : 'Inativo') . "</td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n";
            } else {
                echo "⚠️ Nenhum módulo encontrado na base de dados<br>\n";
            }
            
        } else {
            echo "❌ Erro ao executar getAllTrainingModules()<br>\n";
        }
        
    } else {
        echo "❌ Classe TrainingManager não encontrada<br>\n";
    }
    
    echo "<br><h3>2. Testando via AJAX (como o TrackerGenerator):</h3>\n";
    echo "<div id='ajax_test_result'>Carregando...</div>\n";
    
    echo "<script>
    // Teste AJAX
    fetch('manager/training_integration_manager.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action_type: 'get_training_modules'
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Resposta AJAX:', data);
        
        let resultDiv = document.getElementById('ajax_test_result');
        
        if (data.result === 'success') {
            resultDiv.innerHTML = '✅ AJAX funcionando! Total de módulos: ' + data.total + '<br>';
            if (data.modules && data.modules.length > 0) {
                resultDiv.innerHTML += 'Módulos: ' + data.modules.map(m => m.module_name).join(', ');
            }
        } else {
            resultDiv.innerHTML = '❌ Erro AJAX: ' + (data.error || 'Erro desconhecido');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        document.getElementById('ajax_test_result').innerHTML = '❌ Erro na requisição: ' + error.message;
    });
    </script>\n";
    
    echo "<br><h3>3. Verificação direta no banco:</h3>\n";
    $result = mysqli_query($conn, "SELECT module_id, module_name, module_type, status FROM tb_training_modules ORDER BY created_date DESC");
    
    if ($result) {
        $count = mysqli_num_rows($result);
        echo "✅ Consulta direta executada com sucesso<br>\n";
        echo "<strong>Total de registros:</strong> $count<br>\n";
        
        if ($count > 0) {
            echo "<h4>Registros no banco:</h4>\n";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
            echo "<tr><th>ID</th><th>Nome</th><th>Tipo</th><th>Status</th></tr>\n";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>\n";
                echo "<td>" . htmlspecialchars($row['module_id']) . "</td>\n";
                echo "<td>" . htmlspecialchars($row['module_name']) . "</td>\n";
                echo "<td>" . htmlspecialchars($row['module_type']) . "</td>\n";
                echo "<td>" . ($row['status'] == 1 ? 'Ativo' : 'Inativo') . "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
    } else {
        echo "❌ Erro na consulta: " . mysqli_error($conn) . "<br>\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>\n";
}

echo "<br><p><a href='TrackerGenerator.php'>🔗 Voltar para TrackerGenerator</a></p>\n";
echo "<p><a href='test_training_system.php'>🔍 Verificar sistema</a></p>\n";

mysqli_close($conn);
?>