<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create(
        '/users',
        'GET',
        [],
        [],
        [],
        ['HTTP_ACCEPT' => 'application/json']
    )
);
echo $response->getStatusCode();
