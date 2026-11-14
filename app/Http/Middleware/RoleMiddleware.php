<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Memeriksa apakah pengguna memiliki role yang diizinkan.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Jika pengguna belum login, arahkan ke halaman login.
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
 
        // Jika role pengguna tdk sesuai dgn role yg diminta, arahkan kembali ke dashboard sesuai role sebenarnya.
        if ($user->role !== $role) {
            if ($user->isManager()) {
                return redirect()->route('manager.dashboard');
            }

            if ($user->isCashier()) {
                return redirect()->route('cashier.dashboard');
            }

            
            Auth::logout();// Jika role tdk dikenali, keluarkan pengguna.

            return redirect()->route('login');
        }

        return $next($request);
    }
}
