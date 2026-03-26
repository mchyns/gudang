<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->timestamp('supplier_approved_at')->nullable()->after('price');
            $table->foreignId('supplier_approved_by')->nullable()->after('supplier_approved_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_approved_by');
            $table->dropColumn('supplier_approved_at');
        });
    }
};
