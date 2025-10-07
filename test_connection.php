<?php
// Teste simples de conexão com UserManagement
require_once "spear/manager/session_manager.php";
require_once "spear/config/db.php";

// Simular sessão
session_start();
$_SESSION['client_id'] = 'default_org';

// Testar conexão global
global $conn;

if ($conn) {
    echo "✅ Conexão com banco: OK\n";
    
    // Testar getCurrentClientId
    $client_id = getCurrentClientId();
    echo "✅ Client ID: " . $client_id . "\n";
    
    // Testar query simples
    $result = mysqli_query($conn, "SELECT DATABASE() as db_name");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "✅ Banco atual: " . $row['db_name'] . "\n";
    }
    
    // Testar se tb_departments existe
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'tb_departments'");
    if (mysqli_num_rows($result) > 0) {
        echo "✅ Tabela tb_departments: Existe\n";
    } else {
        echo "❌ Tabela tb_departments: NÃO existe\n";
    }
    
} else {
    echo "❌ Erro na conexão com banco\n";
}
?>