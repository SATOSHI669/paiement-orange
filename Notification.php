<?php
// Reçoit la notification de PawaPay
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Sauvegarde dans un fichier pour voir les notifications (optionnel)
file_put_contents('log.txt', date('Y-m-d H:i:s') . ' - ' . $json . PHP_EOL, FILE_APPEND);

// Répond "OK" à PawaPay (obligatoire)
http_response_code(200);
echo 'OK';
?>
