<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('operational_bensin', 15, 2)->default(0)->after('note');
            $table->decimal('operational_kuli', 15, 2)->default(0)->after('operational_bensin');
            $table->decimal('operational_makan_minum', 15, 2)->default(0)->after('operational_kuli');
            $table->decimal('operational_listrik', 15, 2)->default(0)->after('operational_makan_minum');
            $table->decimal('operational_wifi', 15, 2)->default(0)->after('operational_listrik');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'operational_bensin',
                'operational_kuli',
                'operational_makan_minum',
                'operational_listrik',
                'operational_wifi',
            ]);
        });
    }
};
