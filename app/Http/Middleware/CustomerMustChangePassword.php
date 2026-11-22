<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMustChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $customer = $request->user();

        if ($customer && $customer->must_change_password) {
            return response()->json([
                'message' => 'Silakan ganti password terlebih dahulu.',
                'must_change_password' => true,
            ], 403);
        }

        return $next($request);
    }
}
