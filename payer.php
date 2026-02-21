<?php
// Ton jeton PawaPay (celui du sandbox pour tester)
$PAWAPAY_TOKEN = "eyJraWQiOiIxIiwiYWxnIjoiRVMyNTYifQ.eyJ0dCI6IkFBVCIsInN1YiI6IjE2OTA2IiwibWF2IjoiMSIsImV4cCI6MjA4NzE5ODg5MywiaWF0IjoxNzcxNjY2MDkzLCJwbSI6IkRBRixQQUYiLCJqdGkiOiJiZTQwM2M4My0yMjYyLTRhZmQtOGYwMC0yMDcxODRmMzI2NTgifQ.FOKGwOWYheTCXGUhgRhZluu-6j2TiwYqmlkpWYXZTgXzf06CyGawScPQeXPKBKRhSH58Tm8OLjFzE-WkgV2Oug";

// Reçoit les données envoyées par ton HTML
$input = json_decode(file_get_contents('php://input'), true);

$depositId = $input['depositId'];
$amount = $input['amount'];
$currency = $input['currency'];

// Prépare la requête vers PawaPay
$data = [
    'depositId' => $depositId,
    'returnUrl' => 'https://ton-site.netlify.app/merci.html',
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

if ($http_code == 200) {
    echo $response; // Renvoie la réponse à ton HTML
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur PawaPay']);
}

?>
