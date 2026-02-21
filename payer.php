<?php
// Ton jeton PawaPay (sandbox)
$PAWAPAY_TOKEN = "eyJraWQiOiIxIiwiYWxnIjoiRVMyNTYifQ.eyJ0dCI6IkFBVCIsInN1YiI6IjE2OTA2IiwibWF2IjoiMSIsImV4cCI6MjA4NzE5ODg5MywiaWF0IjoxNzcxNjY2MDkzLCJwbSI6IkRBRixQQUYiLCJqdGkiOiJiZTQwM2M4My0yMjYyLTRhZmQtOGYwMC0yMDcxODRmMzI2NTgifQ.FOKGwOWYheTCXGUhgRhZluu-6j2TiwYqmlkpWYXZTgXzf06CyGawScPQeXPKBKRhSH58Tm8OLjFzE-WkgV2Oug
"; // Remplace par le vrai jeton

// Reçoit les données envoyées (par POST ou GET)
$input = json_decode(file_get_contents('php://input'), true);

// Si pas de données en JSON, on prend les données POST classiques
if (!$input) {
    $input = $_POST;
}

$depositId = $input['depositId'] ?? 'test-' . uniqid();
$amount = $input['amount'] ?? '1000';
$currency = $input['currency'] ?? 'XOF';

// Prépare la requête vers PawaPay
$data = [
    'depositId' => $depositId,
    'returnUrl' => 'https://paiement-orange.onrender.com/merci.html',
    'amount' => $amount,
    'currency' => $currency,
    'reason' => 'Paiement commande'
];

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

// Affiche la réponse complète pour debug
header('Content-Type: application/json');
echo json_encode([
    'http_code' => $http_code,
    'reponse_pawapay' => json_decode($response, true),
    'requete_envoyee' => $data
]);
?>


