<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::latest('id')->first();
echo "Utilisateur: " . $user->email . "\n";
echo "Password hash length: " . strlen($user->password) . "\n";
$info = password_get_info($user->password);
echo "Algo name: " . $info['algoName'] . "\n";
