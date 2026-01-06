<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fine_id')->constrained('fines')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            
            // Payment method & channel
            $table->enum('payment_method', ['cash', 'transfer', 'qris', 'va', 'ewallet'])->default('cash');
            $table->string('payment_channel')->nullable(); // gopay, ovo, bca_va, bni_va, etc
            
            // Gateway references
            $table->string('gateway_order_id')->nullable()->unique();
            $table->string('gateway_transaction_id')->nullable();
            
            // QRIS & VA data
            $table->text('qr_code_url')->nullable(); // URL gambar QRIS
            $table->string('va_number')->nullable(); // Nomor Virtual Account
            
            // Status & timestamps
            $table->enum('status', ['pending', 'success', 'failed', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable(); // Untuk QRIS/VA expiry
            $table->timestamp('paid_at')->nullable();
            
            // Metadata from gateway (JSON)
            $table->json('metadata')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['fine_id', 'status']);
            $table->index('gateway_order_id');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};