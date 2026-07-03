<?php
// ============================================================
//  PROXY.PHP — APIFY API KEY TERSEMBUNYI
//  Letakkan di folder yang sama dengan index.html
// ============================================================

// 🔑 API KEY — AMAN DI BACKEND (GANTI PUNYA LU KALAU MAU)
$APIFY_TOKEN = 'apify_api_f1LFNfCfCdGXyQPbkmEsevfAyNTPQT12r7Nf';

// CORS — Izinkan akses dari domain manapun
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Ambil action dari parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';
$actor = isset($_GET['actor']) ? $_GET['actor'] : '';
$runId = isset($_GET['runId']) ? $_GET['runId'] : '';
$datasetId = isset($_GET['datasetId']) ? $_GET['datasetId'] : '';

if ($action === 'run') {
    // ============================================================
    //  JALANKAN ACTOR APIFY
    // ============================================================
    $input = file_get_contents('php://input');
    $inputData = json_decode($input, true);
    if (empty($inputData)) $inputData = ['helloWorld' => 123];

    $url = "https://api.apify.com/v2/acts/{$actor}/runs?token={$APIFY_TOKEN}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($inputData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        http_response_code(500);
        echo json_encode(['error' => 'Curl error: ' . $error]);
        exit();
    }

    http_response_code($httpCode);
    echo $response;

} elseif ($action === 'status') {
    // ============================================================
    //  CEK STATUS RUN
    // ============================================================
    $url = "https://api.apify.com/v2/actor-runs/{$runId}?token={$APIFY_TOKEN}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        http_response_code(500);
        echo json_encode(['error' => 'Curl error: ' . $error]);
        exit();
    }

    echo $response;

} elseif ($action === 'dataset') {
    // ============================================================
    //  AMBIL HASIL DATASET
    // ============================================================
    $url = "https://api.apify.com/v2/datasets/{$datasetId}/items?token={$APIFY_TOKEN}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        http_response_code(500);
        echo json_encode(['error' => 'Curl error: ' . $error]);
        exit();
    }

    echo $response;

} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action. Use: action=run, status, or dataset']);
}
?>
