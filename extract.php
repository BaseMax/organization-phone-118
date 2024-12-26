<?php
$items = [];

$html_file = 'demo.html';

if (!file_exists($html_file)) {
    die("File not found");
}

$html = file_get_contents($html_file);
$pattern = '/<tbody>(.*?)<\/tbody>/s';

if (preg_match($pattern, $html, $matches)) {
    $tbodyContent = $matches[1];

    $trPattern = '/<tr.*?>(.*?)<\/tr>/s';
    preg_match_all($trPattern, $tbodyContent, $trMatches);

    foreach ($trMatches[0] as $tr) {
        $tdPattern = '/<td.*?>(.*?)<\/td>/s';
        preg_match_all($tdPattern, $tr, $tdMatches);

        unset($tdMatches[1][0]);

        $items[] = $tdMatches[1];
    }
}

print_r($items);

file_put_contents("output.json", json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
