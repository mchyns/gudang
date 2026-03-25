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
            $table->decimal('gaji_internal_kepala_dapur', 18, 2)->default(0)->after('pendapatan_total');
            $table->decimal('gaji_internal_asisten_lapangan', 18, 2)->default(0)->after('gaji_internal_kepala_dapur');
            $table->decimal('gaji_internal_ahli_gizi', 18, 2)->default(0)->after('gaji_internal_asisten_lapangan');
            $table->decimal('gaji_internal_akuntan', 18, 2)->default(0)->after('gaji_internal_ahli_gizi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_distributions', function (Blueprint $table) {
            $table->dropColumn([
                'gaji_internal_kepala_dapur',
                'gaji_internal_asisten_lapangan',
                'gaji_internal_ahli_gizi',
                'gaji_internal_akuntan',
            ]);
        });
    }
};
