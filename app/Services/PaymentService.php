<?php

namespace App\Services;

use App\Models\Fine;
use App\Models\PaymentTransaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $serverKey;
    protected $clientKey;
    protected $isProduction;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->clientKey = config('services.midtrans.client_key');
        // Force sandbox mode for now as requested
        $this->isProduction = false;

        \Midtrans\Config::$serverKey = $this->serverKey;
        \Midtrans\Config::$isProduction = $this->isProduction;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    /**
     * Generate QRIS Payment
     * 
     * @param Fine $fine
     * @return PaymentTransaction
     */
    public function generateQRIS(Fine $fine): PaymentTransaction
    {
        $orderId = $this->generateOrderId($fine);

        // Create payment transaction record
        $transaction = PaymentTransaction::create([
            'fine_id' => $fine->id,
            'user_id' => $fine->user_id,
            'amount' => $fine->amount,
            'payment_method' => 'qris',
            'gateway_order_id' => $orderId,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(15), // QRIS expired 15 menit
        ]);

        try {
            // Call Midtrans Snap API
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $fine->amount,
                ],
                'customer_details' => [
                    'first_name' => $fine->user->name,
                    'email' => $fine->user->email,
                    'phone' => $fine->user->phone ?? '08123456789',
                ],
                'item_details' => [
                    [
                        'id' => 'fine-' . $fine->id,
                        'price' => (int) $fine->amount,
                        'quantity' => 1,
                        'name' => 'Denda Peminjaman Buku',
                    ],
                ],
                'enabled_payments' => ['gopay', 'shopeepay', 'other_qris'],
                'expiry' => [
                    'duration' => 24,
                    'unit' => 'hour',
                ],
                'callbacks' => [
                    'finish' => route('payment.finish'),
                ],
            ];

            $response = \Midtrans\Snap::createTransaction($params);

            // Update transaction dengan Snap info
            $transaction->update([
                'metadata' => [
                    'snap_token' => $response->token,
                    'snap_url' => $response->redirect_url,
                    'raw_response' => (array) $response
                ],
            ]);

            return $transaction;
        } catch (\Exception $e) {
            $transaction->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate Virtual Account
     * 
     * @param Fine $fine
     * @param string $bank (bca, bni, bri, mandiri, permata)
     * @return PaymentTransaction
     */
    public function generateVA(Fine $fine, string $bank = 'bca'): PaymentTransaction
    {
        $orderId = $this->generateOrderId($fine);

        // Create payment transaction record
        $transaction = PaymentTransaction::create([
            'fine_id' => $fine->id,
            'user_id' => $fine->user_id,
            'amount' => $fine->amount,
            'payment_method' => 'va',
            'payment_channel' => $bank . '_va',
            'gateway_order_id' => $orderId,
            'status' => 'pending',
            'expires_at' => now()->addDays(1), // VA expired 1 hari
        ]);

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $fine->amount,
                ],
                'customer_details' => [
                    'first_name' => $fine->user->name,
                    'email' => $fine->user->email,
                    'phone' => $fine->user->phone ?? '08123456789',
                ],
                'enabled_payments' => [$bank . '_va'],
                'callbacks' => [
                    'finish' => route('payment.finish'),
                ],
            ];

            $response = \Midtrans\Snap::createTransaction($params);

            $transaction->update([
                'metadata' => [
                    'snap_token' => $response->token,
                    'snap_url' => $response->redirect_url,
                    'raw_response' => $response
                ],
            ]);

            return $transaction;
        } catch (\Exception $e) {
            $transaction->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Check payment status dari gateway
     * 
     * @param PaymentTransaction $transaction
     * @return array
     */
    public function checkPaymentStatus(PaymentTransaction $transaction): array
    {
        try {
            $status = \Midtrans\Transaction::status($transaction->gateway_order_id);

            return [
                'success' => true,
                'status' => data_get($status, 'transaction_status'),
                'data' => (array) $status,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process webhook dari Midtrans
     * 
     * @param array $notification
     * @return bool
     */
    public function handleWebhook(array $notification): bool
    {
        try {
            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $transactionId = $notification['transaction_id'] ?? null;

            Log::info('Processing Midtrans Notification', ['order_id' => $orderId, 'status' => $transactionStatus]);

            // Find transaction
            $transaction = PaymentTransaction::where('gateway_order_id', $orderId)->first();

            if (!$transaction) {
                Log::error('Transaction not found for webhook', ['order_id' => $orderId]);
                return false;
            }

            // Update status based on transaction status
            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                $transaction->markAsSuccess($transactionId);

                // Process fine payment
                $transaction->fine->processPayment(
                    amount: (float) $transaction->amount,
                    method: $transaction->payment_method,
                    reference: $transactionId
                );
            } elseif ($transactionStatus == 'pending') {
                $transaction->update(['status' => 'pending']);
            } elseif ($transactionStatus == 'expire') {
                $transaction->markAsExpired();
            } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny') {
                $transaction->markAsFailed($transactionStatus);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate unique order ID
     */
    protected function generateOrderId(Fine $fine): string
    {
        return 'FINE-' . $fine->id . '-' . time() . '-' . Str::random(4);
    }

    /**
     * Generate dummy VA number (for testing only)
     */
    protected function generateDummyVA(string $bank): string
    {
        $prefix = match ($bank) {
            'bca' => '1234',
            'bni' => '9876',
            'bri' => '5678',
            'mandiri' => '8900',
            'permata' => '4567',
            default => '0000',
        };

        return $prefix . rand(10000000, 99999999);
    }
}
