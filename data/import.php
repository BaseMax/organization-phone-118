<?php
require_once __DIR__ . "/../load.php";

use Meilisearch\Client;
use Meilisearch\Index;

$jsonFile = __DIR__ . "/output.json";

if (!file_exists($jsonFile)) {
    die("JSON file not found");
}

$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg());
}

echo "JSON data loaded successfully." . PHP_EOL;
echo "Number of items: " . count($data) . PHP_EOL;

$client = new Client($_ENV['MEILI_HOST'], $_ENV['MEILI_MASTER_KEY']);

$indexName = $_ENV['INDEX_NAME'];

try {
    $index = $client->index($indexName);
} catch (Exception $e) {
    die("Error accessing or creating the index: " . $e->getMessage());
}

$documents = [];
foreach ($data as $key => $row) {
    $name = $row[0] ?? null;
    $lastName = $row[1] ?? null;

    if ($name && $lastName) {
        $documents[] = [
            'id' => $key + 1,
            'name' => $name,
            'lastName' => $lastName,
            'phone' => $row[2] ?? null,
            'address' => $row[3] ?? null,
            'position' => $row[4] ?? null,
        ];
    }
}

try {
    $response = $index->addDocuments($documents);
    echo "Data processed successfully: " . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Exception $e) {
    die("Error processing data in Meilisearch: " . $e->getMessage());
}

try {
    $index->updateFilterableAttributes([
        'id',
        'name',
        'lastName',
        'phone',
        'address',
        'position',
    ]);
    echo "Filterable attributes updated successfully." . PHP_EOL;
} catch (Exception $e) {
    die("Error updating filterable attributes: " . $e->getMessage());
}

echo "Data imported and filterable attributes set successfully." . PHP_EOL;
