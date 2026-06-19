<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::with('roles')->get();
foreach ($users as $u) {
    echo $u->email . ' -> ' . $u->roles->pluck('name')->join(', ') . PHP_EOL;
}
