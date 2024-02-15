<?php
$filename = getenv('VERCEL_ENV') === 'production' ? '/tmp/dispositivos.json' : 'dispositivos.json';

// Carrega dados existentes do arquivo JSON
$data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

$deviceId = isset($_COOKIE['device_id']) ? $_COOKIE['device_id'] : uniqid();
$parametro = isset($_GET['request']) ? $_GET['request'] : '';

// Verifica se o dispositivo já existe no array
if (!array_key_exists($deviceId, $data)) {
    // Adiciona novo dispositivo com parâmetro
    $data[$deviceId] = ['last_request' => time(), 'parametro' => $parametro];
} else {
    // Atualiza a data da última solicitação e o parâmetro se já existe
    $data[$deviceId]['last_request'] = time();
    $data[$deviceId]['parametro'] = $parametro;
}

// Remove dispositivos inativos após 30s
foreach ($data as $id => $device) {
    if (time() - $device['last_request'] > 30) {
        unset($data[$id]);
    }
}

// Conta dispositivos no mesmo parâmetro
$dispositivosNoMesmoParametro = array_filter($data, function ($device) use ($parametro) {
    return $device['parametro'] == $parametro;
});

// Salva os dados de volta no arquivo JSON
if (getenv('VERCEL_ENV') === 'production') {
    // No ambiente de produção, evita salvar no arquivo
} else {
    file_put_contents($filename, json_encode($data));
}

// Formata o resultado como JSON
$resultadoJson = json_encode([
    'parametro' => $parametro,
    'dispositivos' => count($dispositivosNoMesmoParametro)
]);

// Define o cabeçalho como JSON
header('Content-Type: application/json');

// Retorna o resultado JSON
echo $resultadoJson;
