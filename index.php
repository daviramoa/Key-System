<?php
header("Content-Type: application/json");

// tenta GET
$key = $_GET["key"] ?? null;
$username = $_GET["username"] ?? null;

// tenta POST JSON
if (!$key || !$username) {
    $data = json_decode(file_get_contents("php://input"), true);
    $key = $key ?? $data["key"] ?? null;
    $username = $username ?? $data["username"] ?? null;
}

// se ainda não tiver key ou username
if (!$key || !$username) {
    echo json_encode([
        "success" => false,
        "message" => "Key ou username não fornecido"
    ]);
    exit;
}

// KEYS VÁLIDAS
$validKeys = [
    "U3RldmVKb3Rhcm8=",
    "RHZ6aW5Hb2RsZXNzRGVtb24=",
    "S2V5QWRtaW5WZXJ5Z29vZA=="
];

// Lista de usernames válidos (exemplo)
$validUsers = [
    "SteveJotaro",
    "DVzinGodlessDemon",
    "AdminVerygood"
];

// valida key + username
if (in_array($key, $validKeys) && in_array($username, $validUsers)) {
    echo json_encode([
        "success" => true,
        "message" => "Key e username válidos"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Key ou username inválido"
    ]);
}
