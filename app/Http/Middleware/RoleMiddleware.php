<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login?
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah role user ada di dalam daftar yang diizinkan?
        // (...$roles memungkinkan kita memasukkan banyak role sekaligus)
        if (!in_array($request->user()->role, $roles)) {
            // Jika tidak punya akses, lempar ke dashboard utama
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}