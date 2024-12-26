<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$meiliHost = $_ENV['MEILI_HOST'];
$meiliApiKey = $_ENV['MEILI_MASTER_KEY'];
$indexName = $_ENV['INDEX_NAME'];

if (empty($_ENV['MEILI_HOST']) || empty($_ENV['MEILI_MASTER_KEY']) || empty($_ENV['INDEX_NAME'])) {
    http_response_code(500);
    echo json_encode(["error" => "Missing Meilisearch configuration in environment variables"]);
    exit;
}