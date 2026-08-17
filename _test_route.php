<?php

use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

try {
    $request = Request::create('/', 'GET');
    $response = $app->handleRequest($request);

    echo 'Status: '.$response->getStatusCode()."\n";
    echo "Content:\n";
    $content = $response->getContent();
    if (strlen($content) > 500) {
        echo substr($content, 0, 500)."\n... (truncated)";
    } else {
        echo $content;
    }
} catch (Throwable $e) {
    echo 'ERROR: '.get_class($e).': '.$e->getMessage()."\n";
    echo $e->getTraceAsString();
}
