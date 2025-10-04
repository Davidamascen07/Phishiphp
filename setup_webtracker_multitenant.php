<?php
echo "<h3>Verificação da Estrutura da Tabela WebTracker</h3>";

$host = "loophish.local";
$username = "root";
$password = "";
$database = "loophish";

// Conectar ao banco
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo "❌ Erro de conexão: " . $conn->connect_error . "<br>";
    exit;
}

echo "✅ Conectado ao banco de dados '$database'<br><br>";

// Verificar se a tabela existe
$result = $conn->query("SHOW TABLES LIKE 'tb_core_web_tracker_list'");
if($result->num_rows == 0) {
    echo "❌ Tabela 'tb_core_web_tracker_list' NÃO encontrada<br>";
    $conn->close();
    exit;
}

echo "✅ Tabela 'tb_core_web_tracker_list' encontrada<br><br>";

// Mostrar estrutura atual
echo "<h4>Estrutura atual da tabela:</h4>";
$result = $conn->query("DESCRIBE tb_core_web_tracker_list");
$has_client_id = false;

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr style='background-color: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Padrão</th></tr>";
while($row = $result->fetch_assoc()) {
    if($row["Field"] == "client_id") {
        $has_client_id = true;
        echo "<tr style='background-color: #d4edda;'>";
    } else {
        echo "<tr>";
    }
    echo "<td>" . $row["Field"] . "</td>";
    echo "<td>" . $row["Type"] . "</td>";
    echo "<td>" . $row["Null"] . "</td>";
    echo "<td>" . ($row["Default"] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table><br>";

// Verificar coluna client_id
if($has_client_id) {
    echo "✅ Coluna 'client_id' já existe na tabela<br>";
    
    // Contar registros com e sem client_id
    $result = $conn->query("SELECT COUNT(*) as total FROM tb_core_web_tracker_list");
    $total = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as with_client FROM tb_core_web_tracker_list WHERE client_id IS NOT NULL AND client_id != ''");
    $with_client = $result->fetch_assoc()['with_client'];
    
    echo "📊 Total de registros: $total<br>";
    echo "📊 Com client_id: $with_client<br>";
    
    if($with_client < $total) {
        echo "⚠️ Alguns registros sem client_id - atualizando...<br>";
        $result = $conn->query("UPDATE tb_core_web_tracker_list SET client_id = 'default_org' WHERE client_id IS NULL OR client_id = ''");
        if($result) {
            echo "✅ Registros atualizados com client_id padrão<br>";
        } else {
            echo "❌ Erro ao atualizar registros: " . $conn->error . "<br>";
        }
    }
} else {
    echo "❌ Coluna 'client_id' NÃO existe - adicionando...<br>";
    
    $sql = "ALTER TABLE tb_core_web_tracker_list ADD COLUMN client_id VARCHAR(50) NOT NULL DEFAULT 'default_org'";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Coluna 'client_id' adicionada com sucesso!<br>";
        
        // Atualizar registros existentes
        $sql_update = "UPDATE tb_core_web_tracker_list SET client_id = 'default_org' WHERE client_id IS NULL OR client_id = ''";
        if ($conn->query($sql_update) === TRUE) {
            echo "✅ Registros existentes atualizados<br>";
        } else {
            echo "❌ Erro ao atualizar registros: " . $conn->error . "<br>";
        }
    } else {
        echo "❌ Erro ao adicionar coluna: " . $conn->error . "<br>";
    }
}

echo "<br><h4>Configuração finalizada!</h4>";
echo "Agora o WebTracker deve funcionar com suporte multi-tenant.<br>";

$conn->close();
?>