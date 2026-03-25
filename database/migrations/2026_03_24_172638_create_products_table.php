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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Harga input dari supplier (belum fix)
            $table->decimal('supplier_price', 15, 2);
            
            // Harga jual fix dari admin ke dapur
            $table->decimal('price', 15, 2)->nullable();
            
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            
            // Status produk: pending (menunggu persetujuan), active, inactive
            $table->string('status')->default('pending'); 
            
            // Fast Moving vs Slow Moving recommendation log?
            $table->string('movement_type')->default('slow'); // fast, slow
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
