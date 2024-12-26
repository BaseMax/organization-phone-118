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
        'filterableAttributes' => ['name', 'lastName', 'phone', 'address', 'position'],
    ]);

    if (!isset($indexCreationTask['taskUid'])) {
        die("Index creation task failed to return taskUid.");
    }

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
    die("Error during index creation: " . $e->getMessage());
}

$documents = [];
foreach ($data as $key => $row) {
    $name = $row[0] ?? null;
    $lastName = $row[1] ?? null;

    if ($name && $lastName) {
        $searchResults = $index->search('', [
            'filter' => "name = '$name' AND lastName = '$lastName'"
        ]);
        
        $existingDocument = $searchResults->getHits();

        if ($existingDocument) {
            $existingId = $existingDocument[0]['id'];
            $documents[] = [
                'id' => $existingId,
                'name' => $name,
                'lastName' => $lastName,
                'phone' => $row[2] ?? $existingDocument[0]['tel'],
                'address' => $row[3] ?? $existingDocument[0]['address'],
                'position' => $row[4] ?? $existingDocument[0]['position'],
            ];
        } else {
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
}

try {
    $response = $index->addDocuments($documents);
    echo "Data processed successfully: " . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Exception $e) {
    die("Error processing data in Meilisearch: " . $e->getMessage());
}
