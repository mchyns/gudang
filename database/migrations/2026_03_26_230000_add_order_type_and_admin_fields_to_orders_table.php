<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type')->default('dapur_sale')->after('user_id');
            $table->text('admin_note')->nullable()->after('note');
            $table->string('shipping_status')->default('pending')->after('status');
            $table->json('operational_extras')->nullable()->after('operational_wifi');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_type',
                'admin_note',
                'shipping_status',
                'operational_extras',
            ]);
        });
    }
};
