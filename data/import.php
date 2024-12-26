<?php
require_once "../_load.php";

use Meilisearch\Client;

$jsonFile = 'output.json';

if (!file_exists($jsonFile)) {
    die("JSON file not found");
}

$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg());
}

$client = new Client($meiliHost, $meiliApiKey);

try {
    $index = $client->createIndex($indexName, [
        'primaryKey' => 'id',
    ]);
} catch (Exception $e) {
    $index = $client->getIndex($indexName);
}

$documents = [];
foreach ($data as $key => $row) {
    $documents[] = [
        'id' => $key + 1,
        'name' => $row[0] ?? null,
        'family' => $row[1] ?? null,
        'tel' => $row[2] ?? null,
        'address' => $row[3] ?? null,
        'position' => $row[4] ?? null,
    ];
}

try {
    $response = $index->addDocuments($documents);
    
    echo "Data inserted successfully: " . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Exception $e) {
    die("Error inserting data into Meilisearch: " . $e->getMessage());
}
