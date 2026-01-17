<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\PaymentService;
use App\Models\Fine;

$fine = Fine::first();
if (!$fine) {
    echo "No fine found\n";
    exit;
}

$service = new PaymentService();
try {
    $transaction = $service->generateQRIS($fine);
    echo "URL: " . data_get($transaction->metadata, 'snap_url') . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
