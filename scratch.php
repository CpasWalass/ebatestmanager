<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = \App\Models\User::where('email', 'chef@ebatest.local')->first();

$request = Illuminate\Http\Request::create('/users', 'GET');
$app->make('auth')->login($user);

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() == 302) {
    echo "Redirect: " . $response->headers->get('Location') . "\n";
} else {
    echo "Response size: " . strlen($response->getContent()) . "\n";
    // echo substr($response->getContent(), 0, 500);
}
