<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // ── Anti-Bot: Honeypot field — bot mengisi field tersembunyi, manusia tidak ──
        if ($request->filled('company_website')) {
            // Balas sukses palsu agar bot tidak tahu form diblokir
            return redirect()->route('home')->with('success', 'Registrasi berhasil!');
        }

        // ── Anti-Bot: Time-trap — submit terlalu cepat dianggap bot ──
        $formStartedAt = (int) $request->input('form_started_at', 0);
        if ($formStartedAt > 0 && (time() - $formStartedAt) < 3) {
            return redirect()->route('home')->with('success', 'Registrasi berhasil!');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s.\'-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => trim($request->name),
            // Normalisasi email ke lowercase untuk mencegah duplikat akun
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'is_admin' => false, // Default user bukan admin
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registrasi berhasil!');
    }
}
