<?php
require "load.php";

header("Content-Type: application/json; charset=UTF-8");

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    $url = $_ENV['MEILI_HOST'] . "/indexes/" . $_ENV['INDEX_NAME'] . "/search";

    $data = json_encode([
        "q" => $query,
        "attributesToRetrieve" => ["name", "lastName", "phone", "address", "position"],
        "limit" => 100
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $_ENV['MEILI_MASTER_KEY']
    ]);

    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        http_response_code(500);
        echo json_encode(["error" => "cURL error occurred: " . $curlError]);
        exit;
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        http_response_code(500);
        echo json_encode(["error" => "Error fetching data from Meilisearch", "status" => $httpCode]);
        exit;
    }

    $results = json_decode($response, true);
    echo json_encode($results["hits"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "An unexpected error occurred: " . $e->getMessage()]);
}
