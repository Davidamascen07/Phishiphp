<?php
echo "🧪 TESTE MANUAL DO ENDPOINT /track\n";
echo "=================================\n\n";

// Dados de teste para page visit
$testData = [
    'page' => 0,
    'trackerId' => 'daxn8c',
    'sess_id' => 'test_' . uniqid(),
    'screen_res' => '1920x1080',
    'rid' => 'manual_test_' . date('His'),
    'ip_info' => null
];

echo "📡 Enviando dados de teste para /track:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Simular POST request
$_POST = ['dummy' => 'value']; // Para passar no isset($_POST)
$GLOBALS['HTTP_RAW_POST_DATA'] = json_encode($testData);

// Capturar output
ob_start();

// Simular entrada JSON
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Test Browser)';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// Incluir o track.php
try {
    // Criar mock do php://input
    $handle = fopen('php://memory', 'r+');
    fwrite($handle, json_encode($testData));
    rewind($handle);
    
    // Override file_get_contents para php://input
    function file_get_contents_mock($filename) {
        global $testData;
        if ($filename === 'php://input') {
            return json_encode($testData);
        }
        return file_get_contents($filename);
    }
    
    echo "🔄 Simulando inclusão do track.php...\n";
    
    // Como não podemos facilmente mockar file_get_contents, vamos testar via cURL
    testWithCurl();
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

function testWithCurl() {
    global $testData;
    
    echo "🌐 Testando com cURL:\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://loophish.local/track');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'User-Agent: Mozilla/5.0 (Test Browser)'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "📊 Resultado:\n";
    echo "   HTTP Code: {$httpCode}\n";
    echo "   Response: {$response}\n";
    
    if ($error) {
        echo "   ❌ cURL Error: {$error}\n";
    }
    
    if ($httpCode === 200 && $response === 'success') {
        echo "   ✅ SUCESSO! Dados foram processados corretamente.\n";
    } else {
        echo "   ⚠️  Resposta inesperada. Verificar logs de erro.\n";
    }
}

echo "\n🔍 Agora testando sem RID:\n";
$testDataNoRid = $testData;
$testDataNoRid['rid'] = '';
unset($testDataNoRid['rid']);

echo "📡 Dados sem RID:\n";
echo json_encode($testDataNoRid, JSON_PRETTY_PRINT) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://loophish.local/track');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testDataNoRid));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: Mozilla/5.0 (Test Browser)'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "📊 Resultado (sem RID):\n";
echo "   HTTP Code: {$httpCode}\n";
echo "   Response: {$response}\n";

if ($httpCode === 200 && $response === 'success') {
    echo "   ✅ SUCESSO! Tracking funciona sem RID.\n";
} else {
    echo "   ⚠️  Problema com tracking sem RID.\n";
}

echo "\n🏁 Teste concluído.\n";
?>