<?php
require_once "spear/config/db.php";
global $conn;

echo "<h1>Verificação Multi-Tenant</h1>\n";

// Verificar estrutura tb_core_mailcamp_list
echo "<h2>Estrutura tb_core_mailcamp_list:</h2>\n";
$result = mysqli_query($conn, "DESCRIBE tb_core_mailcamp_list");
if ($result) {
    echo "<table border='1'>\n";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>\n";
    while ($row = mysqli_fetch_assoc($result)) {
        $color = ($row['Field'] == 'client_id') ? 'background-color: yellow;' : '';
        echo "<tr style='$color'><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>\n";
    }
    echo "</table>\n";
} else {
    echo "Erro: " . mysqli_error($conn);
}

// Verificar estrutura tb_data_mailcamp_live
echo "<h2>Estrutura tb_data_mailcamp_live:</h2>\n";
$result = mysqli_query($conn, "DESCRIBE tb_data_mailcamp_live");
if ($result) {
    echo "<table border='1'>\n";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>\n";
    while ($row = mysqli_fetch_assoc($result)) {
        $color = ($row['Field'] == 'client_id') ? 'background-color: yellow;' : '';
        echo "<tr style='$color'><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>\n";
    }
    echo "</table>\n";
} else {
    echo "Erro: " . mysqli_error($conn);
}

// Verificar dados atuais
echo "<h2>Dados de Campanha (últimos 5):</h2>\n";
$result = mysqli_query($conn, "SELECT campaign_id, camp_name, camp_status, COALESCE(client_id, 'SEM CLIENT_ID') as client_id FROM tb_core_mailcamp_list ORDER BY created_date DESC LIMIT 5");
if ($result) {
    echo "<table border='1'>\n";
    echo "<tr><th>Campaign ID</th><th>Name</th><th>Status</th><th>Client ID</th></tr>\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['campaign_id']}</td><td>{$row['camp_name']}</td><td>{$row['camp_status']}</td><td>{$row['client_id']}</td></tr>\n";
    }
    echo "</table>\n";
}

mysqli_close($conn);
?>