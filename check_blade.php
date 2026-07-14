<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$blade = $app->make('blade.compiler');

$content = file_get_contents('resources/views/livewire/excel-test-editor.blade.php');
try {
    $compiled = $blade->compileString($content);
    echo "BLADE COMPILED SUCCESSFULLY\n";
    // Also try checking PHP syntax of the compiled blade
    file_put_contents('compiled.php', $compiled);
    exec('php -l compiled.php', $out, $ret);
    echo implode("\n", $out);
} catch (Exception $e) {
    echo "BLADE COMPILE ERROR: " . $e->getMessage() . "\n";
}
