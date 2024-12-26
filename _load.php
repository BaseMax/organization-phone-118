<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$meiliHost = $_ENV['MEILI_HOST'];
$meiliApiKey = $_ENV['MEILI_API_KEY'];
$indexName = $_ENV['INDEX_NAME'];

if (empty($meiliUrl) || empty($apiKey) || empty($indexName)) {
    echo json_encode(["error" => "Missing Meilisearch configuration in environment variables"]);
    exit;
}
