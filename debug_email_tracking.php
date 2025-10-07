<?php
// Teste de debug para tracking de email
require_once "spear/config/db.php";

global $conn;

echo "<h1>Debug - Email Campaign Tracking</h1>\n";

// 1. Verificar campanhas ativas
echo "<h2>1. Campanhas Ativas:</h2>\n";
$result = mysqli_query($conn, "SELECT campaign_id, COALESCE(campaign_name, 'No Name') as campaign_name, camp_status FROM tb_core_mailcamp_list ORDER BY created_date DESC LIMIT 5");
if ($result) {
    echo "<table border='1'>\n";
    echo "<tr><th>Campaign ID</th><th>Name</th><th>Status</th></tr>\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['campaign_id']}</td><td>{$row['campaign_name']}</td><td>{$row['camp_status']}</td></tr>\n";
    }
    echo "</table>\n";
} else {
    echo "Erro: " . mysqli_error($conn) . "\n";
}

// 2. Verificar dados de campanha live
echo "<h2>2. Dados de Campanha Live:</h2>\n";
$result = mysqli_query($conn, "SELECT campaign_id, rid, user_email, mail_open_times FROM tb_data_mailcamp_live ORDER BY id DESC LIMIT 5");
if ($result) {
    echo "<table border='1'>\n";
    echo "<tr><th>Campaign ID</th><th>RID</th><th>Email</th><th>Open Times</th></tr>\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['campaign_id']}</td><td>{$row['rid']}</td><td>{$row['user_email']}</td><td>{$row['mail_open_times']}</td></tr>\n";
    }
    echo "</table>\n";
} else {
    echo "Erro: " . mysqli_error($conn) . "\n";
}

// 3. Testar URL de tracking (simulando)
echo "<h2>3. Teste de URL de Tracking:</h2>\n";

// Pegar primeira campanha ativa para teste
$result = mysqli_query($conn, "SELECT campaign_id FROM tb_core_mailcamp_list WHERE camp_status IN (2,4) LIMIT 1");
$campaign_id = null;
if ($result && $row = mysqli_fetch_assoc($result)) {
    $campaign_id = $row['campaign_id'];
}

$result = mysqli_query($conn, "SELECT rid FROM tb_data_mailcamp_live WHERE campaign_id = '$campaign_id' LIMIT 1");
$rid = null;
if ($result && $row = mysqli_fetch_assoc($result)) {
    $rid = $row['rid'];
}

if ($campaign_id && $rid) {
    $test_url = "http://localhost/loophishx/tmail.php?rid=$rid&mid=$campaign_id&mtid=teste29";
    echo "URL de teste: <a href='$test_url' target='_blank'>$test_url</a><br>\n";
    echo "Campaign ID: $campaign_id<br>\n";
    echo "RID: $rid<br>\n";
} else {
    echo "Não foi possível encontrar campanha ativa com dados para teste.\n";
}

// 4. Verificar estrutura das tabelas
echo "<h2>4. Estrutura da Tabela tb_data_mailcamp_live:</h2>\n";
$result = mysqli_query($conn, "DESCRIBE tb_data_mailcamp_live");
if ($result) {
    echo "<table border='1'>\n";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>\n";
    }
    echo "</table>\n";
}

mysqli_close($conn);
?>