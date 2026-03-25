<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = Auth::user()->role;
        $products = collect();

        if ($role === 'supplier') {
            // Supplier only sees their own products
            $products = Product::with('category')
                ->where('supplier_id', Auth::id())
                ->latest()
                ->get();
        } elseif ($role === 'admin' || $role === 'superadmin') {
            // Admin sees all products
            $products = Product::with(['supplier', 'category'])
                ->withSum([
                    'orderItems as total_keluar_qty' => function ($query) {
                        $query->whereHas('order', function ($orderQuery) {
                            $orderQuery->whereIn('status', ['pending', 'processed', 'completed']);
                        });
                    }
                ], 'quantity')
                ->latest()
                ->get();
        }

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only supplier can create
        if (Auth::user()->role !== 'supplier') {
            abort(403);
        }

        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'supplier') {
            abort(403);
        }

        $request->merge([
            'supplier_price' => $this->normalizeNominalInput($request->input('supplier_price')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:30',
            'category_id' => 'required|exists:categories,id',
            'supplier_price' => 'required|numeric|min:0', // Harga mentah
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
        ]);

        $validated['supplier_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['name']) . '-' . time();
        $validated['status'] = 'active'; // Default active
        $validated['movement_type'] = 'slow';
        $validated['initial_stock'] = $validated['stock'];

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($validated['image']);
        }

        $product = Product::create($validated);

        ActivityLogger::log(
            'product.created',
            'Supplier menambahkan produk baru: ' . $product->name,
            $product,
            ['supplier_price' => $product->supplier_price, 'stock' => $product->stock]
        );

        return redirect()->route('supplier.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $role = Auth::user()->role;
        
        // Check permission
        if ($role === 'supplier' && $product->supplier_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $role = Auth::user()->role;

        if ($role === 'supplier') {
            // Supplier can edit base info
            if ($product->supplier_id !== Auth::id()) abort(403);

            $request->merge([
                'supplier_price' => $this->normalizeNominalInput($request->input('supplier_price')),
                'category_id' => $request->input('category_id', $product->category_id),
            ]);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'unit' => 'required|string|max:30',
                'category_id' => 'required|exists:categories,id',
                'supplier_price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:4096',
            ]);

            if ($request->hasFile('image')) {
                $oldImage = $product->image;
                $validated['image'] = $request->file('image')->store('products', 'public');

                if ($oldImage && !str_starts_with($oldImage, 'http') && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            } else {
                unset($validated['image']);
            }

            $product->update($validated);

            ActivityLogger::log(
                'product.updated_by_supplier',
                'Supplier memperbarui produk: ' . $product->name,
                $product,
                ['stock' => $product->stock, 'supplier_price' => $product->supplier_price]
            );

            return redirect()->route('supplier.products.index')->with('success', 'Produk berhasil diupdate.');
        
        } elseif ($role === 'admin' || $role === 'superadmin') {
            // Admin updates the selling price (Fix Price)
            $request->merge([
                'price' => $this->normalizeNominalInput($request->input('price')),
            ]);

            $validated = $request->validate([
                'price' => 'required|numeric|min:0', // Fix selling price
                'movement_type' => 'required|in:fast,slow',
                'status' => 'required|in:active,inactive,pending',
            ]);

            $product->update($validated);

            ActivityLogger::log(
                'product.pricing_updated',
                'Admin memperbarui harga fix produk: ' . $product->name,
                $product,
                ['price' => $product->price, 'status' => $product->status, 'movement_type' => $product->movement_type]
            );

            return redirect()->route('admin.products.index')->with('success', 'Harga jual dan status produk diperbarui.');
        }

        abort(403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if (Auth::user()->role !== 'supplier' || $product->supplier_id !== Auth::id()) {
            abort(403);
        }

        ActivityLogger::log(
            'product.deleted',
            'Supplier menghapus produk: ' . $product->name,
            $product
        );

        $product->delete();
        return redirect()->route('supplier.products.index')->with('success', 'Produk dihapus.');
    }

    private function normalizeNominalInput($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $raw)) {
            $clean = str_replace('.', '', $raw);
            return $clean === '' ? null : $clean;
        }

        if (preg_match('/^\d{1,3}(,\d{3})+$/', $raw)) {
            $clean = str_replace(',', '', $raw);
            return $clean === '' ? null : $clean;
        }

        if (preg_match('/^\d+[\.,]\d+$/', $raw)) {
            $number = (float) str_replace(',', '.', $raw);
            return (string) round($number);
        }

        $clean = preg_replace('/[^0-9]/', '', $raw);

        return $clean === '' ? null : $clean;
    }
}
