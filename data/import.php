<?php
require_once __DIR__ . "/../load.php";

use Meilisearch\Client;
use Meilisearch\Index;

$jsonFile = 'output.json';

if (!file_exists($jsonFile)) {
    die("JSON file not found");
}

$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg());
}

$client = new Client($_ENV['MEILI_HOST'], $_ENV['MEILI_MASTER_KEY']);

try {
    $indexCreationTask = $client->createIndex($_ENV['INDEX_NAME'], [
        'primaryKey' => 'id',
    ]);
    
    while (true) {
        $tasks = $client->getTasks();
        $taskStatus = null;
        
        foreach ($tasks as $task) {
            if ($task['uid'] === $indexCreationTask['taskUid']) {
                $taskStatus = $task['status'];
                break;
            }
        }

        if ($taskStatus === 'succeeded') {
            break;
        }
        if ($taskStatus === 'failed') {
            die("Index creation failed.");
        }
        sleep(1);
    }
    $index = $client->getIndex($_ENV['INDEX_NAME']);
} catch (Exception $e) {
    $index = $client->getIndex($_ENV['INDEX_NAME']);
    
    if (!($index instanceof Meilisearch\Index)) {
        die("Failed to get the Meilisearch index correctly.");
    }
}

$documents = [];
foreach ($data as $key => $row) {
    $name = $row[0] ?? null;
    $family = $row[1] ?? null;

    if ($name && $family) {
        $searchResults = $index->search('', [
            'filter' => "name = '$name' AND family = '$family'"
        ]);
        
        $existingDocument = $searchResults->getHits();

        if ($existingDocument) {
            $existingId = $existingDocument[0]['id'];
            $documents[] = [
                'id' => $existingId,
                'name' => $name,
                'lastName' => $family,
                'phone' => $row[2] ?? $existingDocument[0]['tel'],
                'address' => $row[3] ?? $existingDocument[0]['address'],
                'position' => $row[4] ?? $existingDocument[0]['position'],
            ];
        } else {
            $documents[] = [
                'id' => $key + 1,
                'name' => $name,
                'lastName' => $family,
                'phone' => $row[2] ?? null,
                'address' => $row[3] ?? null,
                'position' => $row[4] ?? null,
            ];
        }
    }
}

try {
    $response = $index->addDocuments($documents);
    echo "Data processed successfully: " . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Exception $e) {
    die("Error processing data in Meilisearch: " . $e->getMessage());
}
