<?php
header("Content-Type: application/json");

// tenta GET
$key = $_GET["key"] ?? null;

// tenta POST JSON
if (!$key) {
    $data = json_decode(file_get_contents("php://input"), true);
    $key = $data["key"] ?? null;
}

// se ainda não tiver key
if (!$key) {
    echo json_encode([
        "success" => false,
        "message" => "Key not provided"
    ]);
    exit;
}

// KEYS VÁLIDAS
$validKeys = [
    "U3RldmVKb3Rhcm8=",
    "RHZ6aW5Hb2RsZXNzRGVtb24=",
    "S2V5QWRtaW5WZXJ5Z29vZA=="
];

if (in_array($key, $validKeys)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid key"
    ]);
}
