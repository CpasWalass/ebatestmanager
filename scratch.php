<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = \App\Models\User::find(1); 
echo "User 1 roles: " . json_encode($u->getRoleNames());

$u2 = \App\Models\User::where('email', 'b.houeto@bubedra.bj')->first();
if ($u2) {
    echo "\nUser b.houeto roles: " . json_encode($u2->getRoleNames());
}
