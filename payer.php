<?php
// Ton jeton PawaPay (sandbox) - À sécuriser en variable d'environnement plus tard
$PAWAPAY_TOKEN = "eyJraWQiOiIxIiwiYWxnIjoiRVMyNTYifQ.eyJ0dCI6IkFBVCIsInN1YiI6IjE2OTA2IiwibWF2IjoiMSIsImV4cCI6MjA4NzIwMDk0OCwiaWF0IjoxNzcxNjY4MTQ4LCJwbSI6IkRBRixQQUYiLCJqdGkiOiIyZmIyOWE5OS03ZTQ0LTRjNzUtOGRjMC1hYTY5NzNhNjhlMzUifQ.FTSuf5JiXPTRUiGA5fHHZLv7DTzkhX-DdxFj3lxpbswKQD6-n3_nPjhvbzV1cTPPwYBQ-xf6zFRK9xk7YygxGA";

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

// Récupère les données envoyées (depuis un formulaire POST, GET, ou JSON)
$input = $_GET; // On utilisera GET car le formulaire sera simple
if (empty($input)) {
    $input = $_POST;
}
if (empty($input)) {
    $input = json_decode(file_get_contents('php://input'), true);
}

// Paramètres par défaut
$depositId = $input['depositId'] ?? gen_uuid();
$amount = $input['amount'] ?? '1000'; // En centimes (1000 = 10 FCFA)
$currency = $input['currency'] ?? 'XOF';
$country = $input['country'] ?? 'SEN'; // Vous pouvez laisser SEN ou rendre dynamique
// L'URL de retour DOIT être celle de votre site Orion Bank (votre merci.html)
$returnUrl = $input['returnUrl'] ?? 'https://votre-site-orion-bank.com/merci.html';

// Structure pour l'API PawaPay v2
$data = [
    'depositId' => $depositId,
    'returnUrl' => $returnUrl,
    'amountDetails' => [
        'amount' => $amount,
        'currency' => $currency
    ],
    'country' => $country,
    'reason' => $input['reason'] ?? 'Dépôt sur Orion Bank'
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

// Gestion de la réponse
if ($http_code === 200 || $http_code === 201) {
    $result = json_decode($response, true);
    if (isset($result['redirectUrl'])) {
        // Redirige immédiatement vers l'URL PawaPay
        header('Location: ' . $result['redirectUrl']);
        exit;
    } else {
        echo "Erreur : Pas d'URL de redirection.";
    }
} else {
    echo "Erreur API PawaPay (code $http_code).";
}
?>





