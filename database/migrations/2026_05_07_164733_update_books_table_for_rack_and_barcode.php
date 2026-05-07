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
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('barcode');
            $table->dropColumn('rack_location');
            $table->foreignId('rack_id')->nullable()->after('available_stock')->constrained('racks')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('barcode', 50)->unique()->nullable()->after('isbn');
            $table->string('rack_location', 50)->nullable()->comment('Format: RAK-A-01');
            $table->dropForeign(['rack_id']);
            $table->dropColumn('rack_id');
        });
    }
};
