<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.login');
}
    public function login(Request $request)
    {
         $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
         if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
                 return $this->redirectByRole();
        }
        return back()
            ->withErrors([
                'username' => 'Username atau password salah.',
            ])
            ->onlyInput('username');
    }
        public function logout(Request $request)
    {
         Auth::logout();
         $request->session()->invalidate();
         $request->session()->regenerateToken();
         return redirect()->route('login');
    }
    private function redirectByRole()
    {
        $user = Auth::user();
        if ($user->isManager()) {
            return redirect()->route('manager.dashboard');
        }
        if ($user->isCashier()) {
            return redirect()->route('cashier.dashboard');
        }
        Auth::logout();
        return redirect()->route('login');
    }

   public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.different' => 'Password baru harus berbeda dari password lama.',
        ]);

        $user = Auth::user();
        $user->password = $validated['password'];
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}