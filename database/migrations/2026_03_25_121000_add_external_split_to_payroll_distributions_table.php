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
        Schema::table('payroll_distributions', function (Blueprint $table) {
            $table->decimal('gaji_eksternal_kodim', 18, 2)->default(0)->after('gaji_internal');
            $table->decimal('gaji_eksternal_koramil', 18, 2)->default(0)->after('gaji_eksternal_kodim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_distributions', function (Blueprint $table) {
            $table->dropColumn(['gaji_eksternal_kodim', 'gaji_eksternal_koramil']);
        });
    }
};
