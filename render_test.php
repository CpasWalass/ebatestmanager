<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$kernel->handle(Illuminate\Http\Request::create('/', 'GET')); // Boot providers

$user = \App\Models\User::where('email', 'chef@ebatest.local')->first();
$app->make('auth')->login($user);

// Render the projets index view
try {
    $html = view('projets.index')->render();
    file_put_contents('rendered_projets.html', $html);
    echo "Rendered successfully. Size: " . strlen($html) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
