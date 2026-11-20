<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles, true)) {
            if ($user->isManager()) {
                return redirect()->route('manager.dashboard');
            }

            if ($user->isCashier()) {
                return redirect()->route('cashier.dashboard');
            }

            Auth::logout();

            return redirect()->route('login');
        }

        return $next($request);
    }
}
