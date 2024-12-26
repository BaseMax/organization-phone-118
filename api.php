<?php
require "_load.php";

header("Content-Type: application/json; charset=UTF-8");

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    $url = $meiliUrl . "/indexes/" . $indexName . "/search";
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
        "Authorization: Bearer " . $apiKey
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
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
