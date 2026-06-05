<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = \App\Models\User::where('email', 'chef@ebatest.local')->first();

$app->make('auth')->guard('web')->login($user);
$request = Illuminate\Http\Request::create('/users', 'GET');
$request->setLaravelSession($app->make('session')->driver());

$response = $kernel->handle($request);
echo "STATUS: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() == 302) {
    echo "REDIRECT TO: " . $response->headers->get('Location') . "\n";
} else {
    echo "OK!";
}
