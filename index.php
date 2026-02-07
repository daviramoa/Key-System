<?php
header("Content-Type: application/json");

$keysFile = __DIR__ . "/keys.json";

/* =========================
   CRIA O ARQUIVO SE NÃO EXISTIR
========================= */
if (!file_exists($keysFile)) {
    file_put_contents($keysFile, json_encode([]));
}

/* =========================
   LÊ AS KEYS
========================= */
$keys = json_decode(file_get_contents($keysFile), true);
if (!is_array($keys)) {
    $keys = [];
}

/* =========================
   GERAR KEY (GET)
========================= */
if (isset($_GET["generate"])) {
    $key = base64_encode(random_bytes(16));

    $keys[] = [
        "key" => $key,
        "used" => false
    ];

    file_put_contents($keysFile, json_encode($keys, JSON_PRETTY_PRINT));

    echo json_encode([
        "success" => true,
        "key" => $key
    ]);
    exit;
}

/* =========================
   VALIDAR KEY (POST)
========================= */
$data = json_decode(file_get_contents("php://input"), true);
$keyInput = $data["key"] ?? null;
$username = $data["username"] ?? null;

if (!$keyInput || !$username) {
    echo json_encode([
        "success" => false,
        "message" => "Key ou username não enviado"
    ]);
    exit;
}

foreach ($keys as &$k) {
    if ($k["key"] === $keyInput && !$k["used"]) {
        $k["used"] = true;
        $k["username"] = $username;

        file_put_contents($keysFile, json_encode($keys, JSON_PRETTY_PRINT));

        echo json_encode([
            "success" => true
        ]);
        exit;
    }
}

echo json_encode([
    "success" => false,
    "message" => "Key inválida ou já usada"
]);
