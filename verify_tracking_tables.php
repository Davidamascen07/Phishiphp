<?php
require_once('spear/config/db.php');

echo "🔍 VERIFICAÇÃO DAS TABELAS DE TRACKING\n";
echo "=====================================\n\n";

// Verificar tabela tb_data_webpage_visit
echo "📄 Verificando tabela tb_data_webpage_visit:\n";
$result = $conn->query("DESCRIBE tb_data_webpage_visit");
if ($result) {
    echo "✅ Tabela existe. Colunas:\n";
    while ($row = $result->fetch_assoc()) {
        echo "   - {$row['Field']} ({$row['Type']})\n";
    }
} else {
    echo "❌ Tabela não existe ou erro: " . $conn->error . "\n";
}

echo "\n📝 Verificando tabela tb_data_webform_submit:\n";
$result = $conn->query("DESCRIBE tb_data_webform_submit");
if ($result) {
    echo "✅ Tabela existe. Colunas:\n";
    while ($row = $result->fetch_assoc()) {
        echo "   - {$row['Field']} ({$row['Type']})\n";
    }
} else {
    echo "❌ Tabela não existe ou erro: " . $conn->error . "\n";
}

echo "\n🔍 Verificando dados existentes para tracker daxn8c:\n";

// Page visits
$result = $conn->query("SELECT COUNT(*) as count FROM tb_data_webpage_visit WHERE tracker_id = 'daxn8c'");
if ($result) {
    $count = $result->fetch_assoc()['count'];
    echo "📄 Page visits: {$count} registros\n";
    
    if ($count > 0) {
        echo "   Últimos 3 registros:\n";
        $result = $conn->query("SELECT rid, public_ip, time, browser FROM tb_data_webpage_visit WHERE tracker_id = 'daxn8c' ORDER BY time DESC LIMIT 3");
        while ($row = $result->fetch_assoc()) {
            $datetime = date('Y-m-d H:i:s', $row['time'] / 1000);
            echo "   - RID: '{$row['rid']}', IP: {$row['public_ip']}, Time: {$datetime}, Browser: {$row['browser']}\n";
        }
    }
} else {
    echo "❌ Erro ao consultar page visits: " . $conn->error . "\n";
}

// Form submits
$result = $conn->query("SELECT COUNT(*) as count FROM tb_data_webform_submit WHERE tracker_id = 'daxn8c'");
if ($result) {
    $count = $result->fetch_assoc()['count'];
    echo "📝 Form submits: {$count} registros\n";
    
    if ($count > 0) {
        echo "   Últimos 3 registros:\n";
        $result = $conn->query("SELECT rid, public_ip, time, page, form_field_data FROM tb_data_webform_submit WHERE tracker_id = 'daxn8c' ORDER BY time DESC LIMIT 3");
        while ($row = $result->fetch_assoc()) {
            $datetime = date('Y-m-d H:i:s', $row['time'] / 1000);
            echo "   - RID: '{$row['rid']}', IP: {$row['public_ip']}, Time: {$datetime}, Page: {$row['page']}, Data: {$row['form_field_data']}\n";
        }
    }
} else {
    echo "❌ Erro ao consultar form submits: " . $conn->error . "\n";
}

echo "\n🔧 Verificando status do tracker daxn8c:\n";
$result = $conn->query("SELECT active, tracker_desc FROM tb_core_web_tracker_list WHERE tracker_id = 'daxn8c'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✅ Tracker encontrado:\n";
    echo "   - Status: " . ($row['active'] ? "✅ Ativo" : "❌ Inativo") . "\n";
    echo "   - Descrição: {$row['tracker_desc']}\n";
} else {
    echo "❌ Tracker daxn8c não encontrado!\n";
}

$conn->close();
echo "\n🏁 Verificação concluída.\n";
?>