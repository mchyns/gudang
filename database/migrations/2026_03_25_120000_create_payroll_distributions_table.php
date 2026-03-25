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
        Schema::create('payroll_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('period_type', 20)->default('manual');
            $table->string('period_label')->nullable();
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();

            $table->decimal('pendapatan_total', 18, 2)->default(0);
            $table->decimal('gaji_internal', 18, 2)->default(0);
            $table->decimal('gaji_eksternal', 18, 2)->default(0);
            $table->decimal('total_gaji', 18, 2)->default(0);
            $table->decimal('sisa_setelah_gaji', 18, 2)->default(0);

            $table->decimal('kepala_dapur_percent', 5, 2)->default(50);
            $table->decimal('kepala_dapur_nominal', 18, 2)->default(0);
            $table->decimal('sisa_untuk_staf', 18, 2)->default(0);

            $table->string('staff_1_name')->default('Staf 1');
            $table->string('staff_2_name')->default('Staf 2');
            $table->string('staff_3_name')->default('Staf 3');
            $table->string('staff_4_name')->default('Staf 4');

            $table->decimal('staff_1_percent', 5, 2)->default(45);
            $table->decimal('staff_2_percent', 5, 2)->default(5);
            $table->decimal('staff_3_percent', 5, 2)->default(30);
            $table->decimal('staff_4_percent', 5, 2)->default(20);

            $table->decimal('staff_1_nominal', 18, 2)->default(0);
            $table->decimal('staff_2_nominal', 18, 2)->default(0);
            $table->decimal('staff_3_nominal', 18, 2)->default(0);
            $table->decimal('staff_4_nominal', 18, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_distributions');
    }
};
