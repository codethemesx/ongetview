<?php
$deviceId = isset($_COOKIE['device_id']) ? $_COOKIE['device_id'] : uniqid();
$parametro = isset($_GET['request']) ? $_GET['request'] : '';

// Envia uma solicitação HTTP para a função JavaScript no Vercel
$url = "https://<seu-vercel-app>/api/hello.js?device_id={$deviceId}&request={$parametro}";

$options = [
    'http' => [
        'method' => 'GET',
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
    ],
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

// Imprime a resposta da função JavaScript
echo $response;
