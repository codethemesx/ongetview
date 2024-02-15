<?php
$filename = getenv('VERCEL_ENV') === 'production' ? '/tmp/dispositivos.json' : 'dispositivos.json';

// Carrega dados existentes do arquivo JSON
$data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

$deviceId = isset($_COOKIE['device_id']) ? $_COOKIE['device_id'] : uniqid();
$parametro = isset($_GET['request']) ? $_GET['request'] : '';

if (array_key_exists($deviceId, $data)) {
    // Atualiza a data da última solicitação e o parâmetro
    $data[$deviceId]['last_request'] = time();
    $data[$deviceId]['parametro'] = $parametro;
} else {
    // Adiciona novo dispositivo com parâmetro
    $data[$deviceId] = ['last_request' => time(), 'parametro' => $parametro];
}

// Remove dispositivos inativos após 30s
foreach ($data as $id => $device) {
    if (time() - $device['last_request'] > 30) {
        unset($data[$id]);
    }
}

// Salva os dados de volta no arquivo JSON
file_put_contents($filename, json_encode($data));

// Conta dispositivos no mesmo parâmetro
$dispositivosNoMesmoParametro = array_filter($data, function ($device) use ($parametro) {
    return $device['parametro'] == $parametro;
});

// Formata o resultado como JSON
$resultadoJson = json_encode([
    'parametro' => $parametro,
    'dispositivos' => count($dispositivosNoMesmoParametro)
]);

// Define o cabeçalho como JSON
header('Content-Type: application/json');

// Retorna o resultado JSON
echo $resultadoJson;
