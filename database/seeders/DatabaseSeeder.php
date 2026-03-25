<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Superadmin (Tidak bisa daftar)
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@hikari.com',
            'role' => 'superadmin',
            'password' => Hash::make('password'),
        ]);

        // 2. Admin Gudang (Tidak bisa daftar)
        User::create([
            'name' => 'Admin Gudang',
            'email' => 'admin@hikari.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // 3. Dummy Supplier
        $supplier = User::create([
            'name' => 'Supplier Sayur Segar',
            'email' => 'supplier@hikari.com',
            'role' => 'supplier',
            'password' => Hash::make('password'),
        ]);
        
        $supplier2 = User::create([
            'name' => 'Supplier Sembako Jaya',
            'email' => 'sembako@hikari.com',
            'role' => 'supplier',
            'password' => Hash::make('password'),
        ]);

        // 4. Dummy Dapur
        User::create([
            'name' => 'Dapur Utama',
            'email' => 'dapur@hikari.com',
            'role' => 'dapur',
            'password' => Hash::make('password'),
        ]);

        // 5. Categories
        $cat1 = Category::create(['name' => 'Sayur', 'slug' => 'sayur']);
        $cat2 = Category::create(['name' => 'Buah', 'slug' => 'buah']);
        $cat3 = Category::create(['name' => 'Sembako', 'slug' => 'sembako']);
        $cat4 = Category::create(['name' => 'Bumbu', 'slug' => 'bumbu']);

        // 6. Products
        Product::create([
            'supplier_id' => $supplier->id,
            'category_id' => $cat1->id,
            'name' => 'Bayam Hijau',
            'slug' => 'bayam-hijau-1kg',
            'description' => 'Bayam hijau segar langsung dari petani',
            'supplier_price' => 5000,
            'price' => 7000, // Fix price set by admin
            'stock' => 50,
            'status' => 'active',
            'movement_type' => 'fast',
        ]);

        Product::create([
            'supplier_id' => $supplier2->id,
            'category_id' => $cat3->id,
            'name' => 'Beras Premium 5kg',
            'slug' => 'beras-premium-5kg',
            'description' => 'Beras pulen kualitas super',
            'supplier_price' => 60000,
            'price' => 65000,
            'stock' => 100,
            'status' => 'active',
            'movement_type' => 'fast',
        ]);
    }
}
