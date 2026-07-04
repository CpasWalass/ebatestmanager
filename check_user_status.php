<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'Wulfredlawadan@gmail.com')->first();
echo "Must change password: " . ($user->must_change_password ? 'Oui' : 'Non') . "\n";
