<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $roleFilter = (string) $request->get('role', '');
        $search = trim((string) $request->get('q', ''));

        $allowedRoles = ['supplier', 'dapur'];

        $partners = User::query()
            ->whereIn('role', $allowedRoles)
            ->when(in_array($roleFilter, $allowedRoles, true), function ($query) use ($roleFilter) {
                $query->where('role', $roleFilter);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%');
                });
            })
            ->withCount(['suppliedProducts', 'orders'])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.partners.index', compact('partners', 'roleFilter', 'search'));
    }

    public function show(User $user)
    {
        if (!in_array($user->role, ['supplier', 'dapur'], true)) {
            abort(404);
        }

        $products = collect();
        $orders = collect();

        if ($user->role === 'supplier') {
            $products = Product::with('category')
                ->where('supplier_id', $user->id)
                ->latest()
                ->paginate(10, ['*'], 'products_page');
        }

        if ($user->role === 'dapur') {
            $orders = Order::with('orderItems.product')
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(10, ['*'], 'orders_page');
        }

        return view('admin.partners.show', compact('user', 'products', 'orders'));
    }
}
