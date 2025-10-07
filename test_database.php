<?php
require_once "spear/config/db.php";
require_once "spear/manager/session_manager.php";

// Inicializar sessão simples para teste
session_start();
$_SESSION['client_id'] = 'default_org';

$conn = mysqli_connect($hostname, $username, $password, $database);
if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}

// Testar se a tabela tb_departments existe
$result = mysqli_query($conn, "SHOW TABLES LIKE 'tb_departments'");
if (mysqli_num_rows($result) > 0) {
    echo "✅ Tabela tb_departments existe\n";
    
    // Verificar estrutura da tabela
    $result = mysqli_query($conn, "DESCRIBE tb_departments");
    echo "\nEstrutura da tabela tb_departments:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "- {$row['Field']} ({$row['Type']}) {$row['Null']} {$row['Key']} {$row['Default']}\n";
    }
} else {
    echo "❌ Tabela tb_departments NÃO existe\n";
}

// Testar getCurrentClientId
require_once "spear/manager/user_management_manager.php";
$client_id = getCurrentClientId();
echo "\nClient ID atual: " . $client_id . "\n";

mysqli_close($conn);
?>