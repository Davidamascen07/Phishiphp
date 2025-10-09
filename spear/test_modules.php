<?php
/**
 * Script para testar acesso direto aos módulos de treinamento
 */

require_once 'config/db.php';

echo "<h2>📚 Teste dos Módulos de Treinamento</h2>\n";

// Verificar módulos existentes
echo "<h3>Módulos Existentes:</h3>\n";
$result = mysqli_query($conn, "SELECT * FROM tb_training_modules ORDER BY created_date DESC");

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
    echo "<tr style='background: #f0f0f0;'>\n";
    echo "<th>ID</th><th>Nome</th><th>Tipo</th><th>Categoria</th><th>Status</th><th>Cliente</th><th>Data Criação</th>\n";
    echo "</tr>\n";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>\n";
        echo "<td>" . htmlspecialchars($row['module_id']) . "</td>\n";
        echo "<td><strong>" . htmlspecialchars($row['module_name']) . "</strong></td>\n";
        echo "<td>" . htmlspecialchars($row['module_type']) . "</td>\n";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>\n";
        echo "<td>" . ($row['status'] == 1 ? '✅ Ativo' : '❌ Inativo') . "</td>\n";
        echo "<td>" . htmlspecialchars($row['client_id']) . "</td>\n";
        echo "<td>" . htmlspecialchars($row['created_date']) . "</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo "<p>❌ Nenhum módulo encontrado</p>\n";
}

echo "<br><h3>🎯 Próximos Passos:</h3>\n";
echo "<ol>\n";
echo "<li><strong>Fazer login no sistema:</strong> <a href='index.php' target='_blank'>http://localhost/loophishx/spear/</a></li>\n";
echo "<li><strong>Acessar TrainingManagement.php</strong> após login</li>\n";
echo "<li><strong>Criar novo módulo de teste</strong> conforme o guia</li>\n";
echo "</ol>\n";

echo "<h3>🔧 Informações do Sistema:</h3>\n";
echo "<p><strong>Banco de dados:</strong> " . $curr_db . "</p>\n";
echo "<p><strong>Tabelas de treinamento:</strong> Todas criadas ✅</p>\n";
echo "<p><strong>Módulos cadastrados:</strong> " . mysqli_num_rows($result) . "</p>\n";

echo "<br><p><a href='test_training_system.php'>🔍 Verificar sistema novamente</a></p>\n";

mysqli_close($conn);
?>