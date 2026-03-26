<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('dapur_adjustment_note')->nullable()->after('drop_date');
            $table->timestamp('dapur_sales_note_locked_at')->nullable()->after('dapur_adjustment_note');
            $table->foreignId('dapur_sales_note_locked_by')->nullable()->after('dapur_sales_note_locked_at')->constrained('users')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('dapur_final_quantity')->nullable()->after('quantity');
            $table->text('dapur_item_note')->nullable()->after('dapur_final_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['dapur_final_quantity', 'dapur_item_note']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dapur_sales_note_locked_by');
            $table->dropColumn(['dapur_adjustment_note', 'dapur_sales_note_locked_at']);
        });
    }
};
