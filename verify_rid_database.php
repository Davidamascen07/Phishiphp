<?php
require_once('spear/config/db.php');

echo "🔍 VERIFICAÇÃO DETALHADA - RID NO BANCO DE DADOS\n";
echo "===============================================\n\n";

// Verificar os dados mais recentes do tracker daxn8c
echo "📄 ÚLTIMAS PAGE VISITS (daxn8c):\n";
echo "================================\n";
$result = $conn->query("
    SELECT 
        id,
        tracker_id,
        session_id,
        rid,
        CHAR_LENGTH(rid) as rid_length,
        public_ip,
        FROM_UNIXTIME(time/1000) as formatted_time,
        browser,
        platform
    FROM tb_data_webpage_visit 
    WHERE tracker_id = 'daxn8c' 
    ORDER BY time DESC 
    LIMIT 5
");

if ($result && $result->num_rows > 0) {
    echo sprintf("%-5s %-10s %-15s %-20s %-12s %-15s %-20s\n", 
        "ID", "Tracker", "Session", "RID", "RID_Length", "IP", "Time");
    echo str_repeat("-", 100) . "\n";
    
    while($row = $result->fetch_assoc()) {
        echo sprintf("%-5s %-10s %-15s %-20s %-12s %-15s %-20s\n",
            $row['id'],
            $row['tracker_id'],
            substr($row['session_id'], 0, 14),
            $row['rid'] === '' ? '(VAZIO)' : "'{$row['rid']}'",
            $row['rid_length'],
            $row['public_ip'],
            $row['formatted_time']
        );
    }
} else {
    echo "❌ Nenhum page visit encontrado.\n";
}

echo "\n📝 ÚLTIMOS FORM SUBMITS (daxn8c):\n";
echo "=================================\n";
$result = $conn->query("
    SELECT 
        id,
        tracker_id,
        session_id,
        rid,
        CHAR_LENGTH(rid) as rid_length,
        public_ip,
        page,
        FROM_UNIXTIME(time/1000) as formatted_time,
        form_field_data
    FROM tb_data_webform_submit 
    WHERE tracker_id = 'daxn8c' 
    ORDER BY time DESC 
    LIMIT 5
");

if ($result && $result->num_rows > 0) {
    echo sprintf("%-5s %-10s %-15s %-20s %-12s %-15s %-20s\n", 
        "ID", "Tracker", "Session", "RID", "RID_Length", "Page", "Time");
    echo str_repeat("-", 100) . "\n";
    
    while($row = $result->fetch_assoc()) {
        echo sprintf("%-5s %-10s %-15s %-20s %-12s %-15s %-20s\n",
            $row['id'],
            $row['tracker_id'],
            substr($row['session_id'], 0, 14),
            $row['rid'] === '' ? '(VAZIO)' : "'{$row['rid']}'",
            $row['rid_length'],
            $row['page'],
            $row['formatted_time']
        );
        echo "   Form Data: " . substr($row['form_field_data'], 0, 50) . "\n";
    }
} else {
    echo "❌ Nenhum form submit encontrado.\n";
}

echo "\n🔍 VERIFICAÇÃO ESPECÍFICA - REGISTROS COM RID NÃO VAZIO:\n";
echo "========================================================\n";

// Page visits com RID não vazio
$result = $conn->query("
    SELECT 
        COUNT(*) as total_with_rid,
        COUNT(CASE WHEN rid = '' THEN 1 END) as empty_rid,
        COUNT(CASE WHEN rid != '' THEN 1 END) as non_empty_rid
    FROM tb_data_webpage_visit 
    WHERE tracker_id = 'daxn8c'
");

if ($result && $result->num_rows > 0) {
    $stats = $result->fetch_assoc();
    echo "📊 ESTATÍSTICAS PAGE VISITS:\n";
    echo "   Total de registros: {$stats['total_with_rid']}\n";
    echo "   RID vazio: {$stats['empty_rid']}\n";
    echo "   RID com valor: {$stats['non_empty_rid']}\n";
}

// Form submits com RID não vazio
$result = $conn->query("
    SELECT 
        COUNT(*) as total_with_rid,
        COUNT(CASE WHEN rid = '' THEN 1 END) as empty_rid,
        COUNT(CASE WHEN rid != '' THEN 1 END) as non_empty_rid
    FROM tb_data_webform_submit 
    WHERE tracker_id = 'daxn8c'
");

if ($result && $result->num_rows > 0) {
    $stats = $result->fetch_assoc();
    echo "\n📊 ESTATÍSTICAS FORM SUBMITS:\n";
    echo "   Total de registros: {$stats['total_with_rid']}\n";
    echo "   RID vazio: {$stats['empty_rid']}\n";
    echo "   RID com valor: {$stats['non_empty_rid']}\n";
}

echo "\n🔍 EXEMPLOS DE RIDs NÃO VAZIOS (se houver):\n";
echo "==========================================\n";

$result = $conn->query("
    (SELECT 'page_visit' as type, rid, FROM_UNIXTIME(time/1000) as time 
     FROM tb_data_webpage_visit 
     WHERE tracker_id = 'daxn8c' AND rid != '' 
     ORDER BY time DESC LIMIT 3)
    UNION ALL
    (SELECT 'form_submit' as type, rid, FROM_UNIXTIME(time/1000) as time 
     FROM tb_data_webform_submit 
     WHERE tracker_id = 'daxn8c' AND rid != '' 
     ORDER BY time DESC LIMIT 3)
    ORDER BY time DESC
");

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "   {$row['type']}: '{$row['rid']}' ({$row['time']})\n";
    }
} else {
    echo "❌ Nenhum registro com RID não vazio encontrado.\n";
    echo "   Isso confirma que o problema é que todos os RIDs estão sendo salvos como string vazia.\n";
}

echo "\n🔧 TESTE MANUAL DE INSERT:\n";
echo "=========================\n";

// Testar insert manual
$test_rid = 'manual_insert_test_' . date('His');
$test_session = 'manual_session_' . uniqid();
$test_time = round(microtime(true) * 1000);

$stmt = $conn->prepare("
    INSERT INTO tb_data_webpage_visit
    (tracker_id, session_id, rid, public_ip, ip_info, user_agent, screen_res, time, browser, platform, device_type) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param('sssssssssss', 
    $tracker_id, $session_id, $rid, $public_ip, $ip_info, $user_agent, $screen_res, $date_time, $browser, $platform, $device_type
);

// Valores de teste
$tracker_id = 'daxn8c';
$session_id = $test_session;
$rid = $test_rid;
$public_ip = '127.0.0.1';
$ip_info = '{"test": true}';
$user_agent = 'Manual Test Agent';
$screen_res = '1920x1080';
$date_time = $test_time;
$browser = 'Test Browser';
$platform = 'Test Platform';
$device_type = 'Desktop';

if ($stmt->execute()) {
    echo "✅ Insert manual executado com sucesso!\n";
    echo "   RID inserido: '$test_rid'\n";
    echo "   Session: '$test_session'\n";
    
    // Verificar se foi salvo corretamente
    $verify = $conn->query("SELECT rid FROM tb_data_webpage_visit WHERE session_id = '$test_session'");
    if ($verify && $verify->num_rows > 0) {
        $saved_rid = $verify->fetch_assoc()['rid'];
        echo "   RID salvo no banco: '$saved_rid'\n";
        echo "   ✅ Insert manual funcionou corretamente!\n";
    }
} else {
    echo "❌ Erro no insert manual: " . $stmt->error . "\n";
}

$conn->close();
echo "\n🏁 Verificação concluída.\n";
?>