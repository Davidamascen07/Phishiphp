<?php
require_once(dirname(__FILE__) . '/spear/config/db.php');

// Conexão direta para setup
$conn = new mysqli($HOST, $USERNAME, $PASSWORD, $DB_NAME);

echo "Verificando estrutura da tabela tb_core_web_tracker_list...<br><br>";

// Verificar se a coluna client_id existe
$result = $conn->query("SHOW COLUMNS FROM tb_core_web_tracker_list LIKE 'client_id'");
if($result->num_rows > 0) {
    echo "✅ Coluna 'client_id' já existe na tabela tb_core_web_tracker_list<br>";
} else {
    echo "❌ Coluna 'client_id' NÃO existe na tabela tb_core_web_tracker_list<br>";
    echo "Adicionando coluna client_id...<br>";
    
    // Adicionar a coluna client_id
    $sql = "ALTER TABLE tb_core_web_tracker_list ADD COLUMN client_id VARCHAR(50) DEFAULT 'default_org'";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Coluna 'client_id' adicionada com sucesso!<br>";
        
        // Atualizar registros existentes sem client_id
        $sql_update = "UPDATE tb_core_web_tracker_list SET client_id = 'default_org' WHERE client_id IS NULL OR client_id = ''";
        if ($conn->query($sql_update) === TRUE) {
            echo "✅ Registros existentes atualizados com client_id padrão<br>";
        } else {
            echo "❌ Erro ao atualizar registros: " . $conn->error . "<br>";
        }
    } else {
        echo "❌ Erro ao adicionar coluna: " . $conn->error . "<br>";
    }
}

echo "<br>Estrutura atual da tabela:<br>";
$result = $conn->query("DESCRIBE tb_core_web_tracker_list");
if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Padrão</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["Field"] . "</td><td>" . $row["Type"] . "</td><td>" . $row["Null"] . "</td><td>" . $row["Default"] . "</td></tr>";
    }
    echo "</table>";
}

$conn->close();
?>