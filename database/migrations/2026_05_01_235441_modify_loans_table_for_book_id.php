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
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['book_item_id']);
            $table->dropIndex(['book_item_id', 'status']);
            $table->dropColumn('book_item_id');

            $table->foreignId('book_id')->after('user_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1)->after('book_id');

            // Re-create the index for the new column
            $table->index(['book_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['book_id']);
            $table->dropIndex(['book_id', 'status']);
            $table->dropColumn(['book_id', 'quantity']);

            $table->foreignId('book_item_id')->after('user_id')->constrained()->onDelete('cascade');
            $table->index(['book_item_id', 'status']);
        });
    }
};
