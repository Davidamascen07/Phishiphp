<?php
require_once(dirname(__FILE__) . '/spear/manager/session_manager.php');

if(isSessionValid() == false) {
    echo "Sessão inválida";
    exit;
}

$client_id = getCurrentClientId();
echo "Client ID atual: " . $client_id . "<br><br>";

// Verificar quantos trackers existem para este cliente
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM tb_core_web_tracker_list WHERE client_id = ?");
$stmt->bind_param('s', $client_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
echo "Total de trackers para o cliente: " . $row['total'] . "<br><br>";

// Listar todos os trackers
$stmt = $conn->prepare("SELECT tracker_id, tracker_name, date, client_id FROM tb_core_web_tracker_list WHERE client_id = ?");
$stmt->bind_param('s', $client_id);
$stmt->execute();
$result = $stmt->get_result();

echo "Lista de trackers:<br>";
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['tracker_id'] . " | Nome: " . $row['tracker_name'] . " | Data: " . $row['date'] . " | Cliente: " . $row['client_id'] . "<br>";
}

// Verificar todos os trackers (para debug)
echo "<br><br>TODOS os trackers na tabela:<br>";
$stmt = $conn->prepare("SELECT tracker_id, tracker_name, date, client_id FROM tb_core_web_tracker_list");
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['tracker_id'] . " | Nome: " . $row['tracker_name'] . " | Data: " . $row['date'] . " | Cliente: " . $row['client_id'] . "<br>";
}

$stmt->close();
?>