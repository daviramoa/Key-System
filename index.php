<?php
// Ativa exibição de erros apenas durante desenvolvimento (comente em produção se quiser)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sempre responde como JSON
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// Arquivo de keys
$keysFile = __DIR__ . '/keys.json';

// Cria o arquivo se não existir
if (!file_exists($keysFile)) {
    $initial = [];
    if (file_put_contents($keysFile, json_encode($initial, JSON_PRETTY_PRINT)) === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Não foi possível criar o arquivo keys.json']);
        exit;
    }
}

// Lê o arquivo de keys
$keysJson = file_get_contents($keysFile);
if ($keysJson === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Não foi possível ler keys.json']);
    exit;
}

$keys = json_decode($keysJson, true);
if (!is_array($keys)) {
    $keys = [];
}

// --------------------------
// ROTA: Gerar nova key (GET ?generate)
// --------------------------
if (isset($_GET['generate'])) {
    try {
        $key = base64_encode(random_bytes(16));
        $username = 'User' . random_int(1000, 9999);

        $keys[] = [
            'key'      => $key,
            'username' => $username,
            'used'     => false,
            'created'  => date('c'),
            'ip'       => $_SERVER['REMOTE_ADDR'] ?? 'unknown' // opcional para debug
        ];

        if (file_put_contents($keysFile, json_encode($keys, JSON_PRETTY_PRINT)) === false) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Falha ao salvar nova key']);
            exit;
        }

        echo json_encode([
            'success'  => true,
            'key'      => $key,
            'username' => $username
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error'   => 'Erro ao gerar key: ' . $e->getMessage()
        ]);
    }
    exit;
}

// --------------------------
// ROTA: Validar key (POST JSON)
// --------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputRaw = file_get_contents('php://input');
    $input = json_decode($inputRaw, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'JSON inválido no corpo da requisição']);
        exit;
    }

    $key = trim($input['key'] ?? '');
    $username = trim($input['username'] ?? '');

    if (empty($key) || empty($username)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'key e username são obrigatórios']);
        exit;
    }

    $found = false;
    foreach ($keys as &$entry) {
        if ($entry['key'] === $key && $entry['username'] === $username) {
            $found = true;

            if ($entry['used']) {
                echo json_encode([
                    'success' => false,
                    'error'   => 'Esta key já foi utilizada'
                ]);
            } else {
                $entry['used'] = true;
                $entry['used_at'] = date('c');
                $entry['used_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

                if (file_put_contents($keysFile, json_encode($keys, JSON_PRETTY_PRINT)) === false) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Falha ao marcar key como usada']);
                    exit;
                }

                echo json_encode(['success' => true]);
            }
            break;
        }
    }

    if (!$found) {
        echo json_encode([
            'success' => false,
            'error'   => 'Key ou username inválido'
        ]);
    }
    exit;
}

// --------------------------
// Resposta padrão (raiz ou qualquer outra requisição)
// --------------------------
http_response_code(200);
echo json_encode([
    'success' => true,
    'status'  => 'online',
    'message' => 'Key System API rodando. Use ?generate para criar uma key nova. Envie POST JSON com {"key": "...", "username": "..."} para validar.',
    'time'    => date('c'),
    'total_keys' => count($keys)
]);
