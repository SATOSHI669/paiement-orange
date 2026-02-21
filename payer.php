<?php
// Ton jeton PawaPay (sandbox)
$PAWAPAY_TOKEN = "eyJraWQiOiIxIiwiYWxnIjoiRVMyNTYifQ.eyJ0dCI6IkFBVCIsInN1YiI6IjE2OTA2IiwibWF2IjoiMSIsImV4cCI6MjA4NzIwMDk0OCwiaWF0IjoxNzcxNjY4MTQ4LCJwbSI6IkRBRixQQUYiLCJqdGkiOiIyZmIyOWE5OS03ZTQ0LTRjNzUtOGRjMC1hYTY5NzNhNjhlMzUifQ.FTSuf5JiXPTRUiGA5fHHZLv7DTzkhX-DdxFj3lxpbswKQD6-n3_nPjhvbzV1cTPPwYBQ-xf6zFRK9xk7YygxGA"; // Mets ton vrai jeton

// Fonction pour générer un UUID v4 valide
function gen_uuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Récupère les données envoyées
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$depositId = $input['depositId'] ?? gen_uuid();
$amount = $input['amount'] ?? '1000';
$currency = $input['currency'] ?? 'XOF';

// Structure CORRECTE pour l'API PawaPay
$data = [
    'depositId' => $depositId,
    'returnUrl' => 'https://paiement-orange.onrender.com/merci.html',
    'amountDetails' => [
        'amount' => $amount,
        'currency' => $currency
    ],
    'country' => 'SEN', // Pour fixer le pays (Sénégal)
    'reason' => 'Paiement commande'
];

// Envoi à l'API PawaPay
$ch = curl_init('https://api.sandbox.pawapay.io/v2/paymentpage');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $PAWAPAY_TOKEN,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Affiche la réponse
header('Content-Type: application/json');
echo json_encode([
    'http_code' => $http_code,
    'reponse_pawapay' => json_decode($response, true),
    'requete_envoyee' => $data
]);
?>




