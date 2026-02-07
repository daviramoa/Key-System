<?php
header("Content-Type: application/json");

$keysFile = "keys.json";
$keys = json_decode(file_get_contents($keysFile), true);

// gera key nova
function generateKey() {
    return base64_encode(random_bytes(16));
}

// salva no arquivo
function saveKeys($keys, $file) {
    file_put_contents($file, json_encode($keys, JSON_PRETTY_PRINT));
}

// ============================
// GERAR KEY
// ============================
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["generate"])) {
    $newKey = generateKey();

    $keys[] = [
        "key" => $newKey,
        "username" => null,
        "used" => false
    ];

    saveKeys($keys, $keysFile);

    echo json_encode([
        "success" => true,
        "key" => $newKey
    ]);
    exit;
}

// ============================
// VALIDAR KEY + USERNAME
// ============================
$data = json_decode(file_get_contents("php://input"), true);

$key = $data["key"] ?? null;
$username = $data["username"] ?? null;

if (!$key || !$username) {
    echo json_encode([
        "success" => false,
        "message" => "Key ou username não fornecido"
    ]);
    exit;
}

foreach ($keys as &$k) {
    if ($k["key"] === $key) {

        if ($k["used"]) {
            echo json_encode([
                "success" => false,
                "message" => "Key já usada"
            ]);
            exit;
        }

        $k["used"] = true;
        $k["username"] = $username;
        saveKeys($keys, $keysFile);

        echo json_encode([
            "success" => true
        ]);
        exit;
    }
}

echo json_encode([
    "success" => false,
    "message" => "Key inválida"
]);
