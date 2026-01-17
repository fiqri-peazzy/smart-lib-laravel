<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use Midtrans\Config;
use Midtrans\Snap;

Config::$serverKey = 'SB-Mid-server-k3MqmB3_nKFoAoztKnt9Hv7U';
Config::$isProduction = false;

$params = [
    'transaction_details' => [
        'order_id' => 'TEST-' . time(),
        'gross_amount' => 10000,
    ],
    'enabled_payments' => ['gopay', 'shopeepay', 'other_qris'],
];

try {
    $res = Snap::createTransaction($params);
    echo "URL: " . $res->redirect_url . "\n";
    echo "Snapshot Token: " . $res->token . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
