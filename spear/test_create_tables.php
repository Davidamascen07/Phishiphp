<?php
echo "Content-Type: application/json\n\n";

// Simular requisição para criar tabelas
$_POST = array('action_type' => 'create_tables');

try {
    include('manager/training_manager.php');
} catch (Exception $e) {
    echo json_encode(['result' => 'failed', 'error' => 'Exception: ' . $e->getMessage()]);
} catch (Error $e) {
    echo json_encode(['result' => 'failed', 'error' => 'Fatal Error: ' . $e->getMessage()]);
}
?>