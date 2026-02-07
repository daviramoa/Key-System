<?php
header("Content-Type: application/json");

$keys = [
    "ABC-123-XYZ",
    "SINTAXE-KEY-001",
    "FREE-KEY-2026"
];

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["key"])) {
    echo json_encode([
        "success" => false,
        "message" => "Key not provided"
    ]);
    exit;
}

$key = trim($data["key"]);

if (in_array($key, $keys)) {
    echo json_encode([
        "success" => true
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid key"
    ]);
}
