<?php
header("Content-Type: application/json; charset=UTF-8");

define("MEILISEARCH_URL", "http://127.0.0.1:7700");
define("API_KEY", "your_meilisearch_api_key");
define("INDEX_NAME", "your_index_name");

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    $url = MEILISEARCH_URL . "/indexes/" . INDEX_NAME . "/search";
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
        "Authorization: Bearer " . API_KEY
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        echo json_encode(["error" => "Error fetching data from Meilisearch", "status" => $httpCode]);
        exit;
    }

    $results = json_decode($response, true);
    echo json_encode($results["hits"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(["error" => "An unexpected error occurred: " . $e->getMessage()]);
}
