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
        Schema::create('loan_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Statistics
            $table->unsignedInteger('total_loans')->default(0);
            $table->unsignedInteger('on_time_returns')->default(0);
            $table->unsignedInteger('late_returns')->default(0);
            $table->unsignedInteger('total_extensions')->default(0);

            // Fines
            $table->decimal('total_fines_incurred', 10, 2)->default(0);
            $table->decimal('total_fines_paid', 10, 2)->default(0);

            // Current Status
            $table->unsignedInteger('active_loans')->default(0);
            $table->unsignedInteger('overdue_loans')->default(0);

            // Credit Score Components
            $table->decimal('calculated_score', 5, 2)->default(100.00);

            // Last Activity
            $table->timestamp('last_loan_at')->nullable();
            $table->timestamp('last_return_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->unique('user_id');
            $table->index('calculated_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_history');
    }
};
