<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View { return view('auth.login'); }
    public function register(): View { return view('auth.register', ['kelas' => Kelas::where('is_active', true)->orderBy('nama')->get()]); }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        return redirect()->intended(Auth::user()->isAdmin() ? route('admin.dashboard') : route('catalog'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'phone' => preg_replace('/[\s()-]+/', '', (string) $request->input('phone')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^(?:\+62|62|0)8[1-9][0-9]{6,11}$/'],
            'kelas_id' => ['nullable', 'exists:kelas,id'], 'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.regex' => 'Masukkan nomor HP Indonesia yang valid, misalnya 081234567890.',
        ]);
        $user = User::create($data);
        Auth::login($user);
        return redirect()->route('catalog')->with('success', 'Akun berhasil dibuat. Selamat datang di ALFAGO!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('catalog');
    }
}
