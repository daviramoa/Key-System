<?php
error_reporting(0);
header("Content-Type: application/json");

$keysFile = __DIR__ . "/keys.json";

// cria o arquivo se não existir
if (!file_exists($keysFile)) {
    file_put_contents($keysFile, json_encode([]));
}

// lê as keys
$keys = json_decode(file_get_contents($keysFile), true);
if (!is_array($keys)) {
    $keys = [];
}

// gerar key + username diretamente
if (isset($_GET["generate"])) {
    // gerar key aleatória
    $key = base64_encode(random_bytes(16));

    // gerar username aleatório
    $username = "User" . rand(1000, 9999);

    // salva no keys.json
    $keys[] = [
        "key" => $key,
        "username" => $username,
        "used" => false
    ];
    file_put_contents($keysFile, json_encode($keys, JSON_PRETTY_PRINT));

    // retorna tudo de uma vez
    echo json_encode([
        "success" => true,
        "key" => $key,
        "username" => $username
    ]);
    exit;
}
