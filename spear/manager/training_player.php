<?php
require_once(dirname(__FILE__) . '/manager/session_manager.php');
// Note: this page assumes user is logged in; for anonymous/employee view you may remove session requirement
isSessionValid(true);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Treinamento - Player</title>
    <link rel="stylesheet" href="../css/style.min.css">
</head>
<body>
    <?php include 'z_menu.php'; ?>
    <div class="container mt-4">
        <div id="player_container">
            <h3 id="module_title">Carregando...</h3>
            <div id="module_content"></div>
            <hr>
            <div id="quiz_area"></div>
        </div>
    </div>

    <script src="../js/libs/jquery/jquery-3.6.0.min.js"></script>
    <script src="../js/training_player.js"></script>
</body>
</html>
