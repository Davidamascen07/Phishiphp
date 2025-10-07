<?php
require_once "spear/config/db.php";
global $conn;

// Verificar se tb_core_mailcamp_list tem client_id
$result = mysqli_query($conn, "SHOW COLUMNS FROM tb_core_mailcamp_list LIKE 'client_id'");
$has_client_id_mailcamp = mysqli_num_rows($result) > 0;

// Verificar se tb_data_mailcamp_live tem client_id
$result = mysqli_query($conn, "SHOW COLUMNS FROM tb_data_mailcamp_live LIKE 'client_id'");
$has_client_id_live = mysqli_num_rows($result) > 0;

echo "<h1>Verificação Multi-tenant para Email Tracking</h1>";
echo "<p>tb_core_mailcamp_list tem client_id: " . ($has_client_id_mailcamp ? "✅ SIM" : "❌ NÃO") . "</p>";
echo "<p>tb_data_mailcamp_live tem client_id: " . ($has_client_id_live ? "✅ SIM" : "❌ NÃO") . "</p>";

if ($has_client_id_mailcamp && $has_client_id_live) {
    echo "<h2>✅ Sistema está preparado para multi-tenant</h2>";
} else {
    echo "<h2>❌ Sistema NÃO está preparado para multi-tenant</h2>";
    echo "<p>As funções verifyMailCmapaign e verifyMailCmapaignUser precisam ser atualizadas</p>";
}
?>