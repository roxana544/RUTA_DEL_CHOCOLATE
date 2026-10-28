<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('account.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:40'],
            'origin_city' => ['nullable', 'string', 'max:80'],
        ]);
        $data['marketing_consent'] = $request->boolean('marketing_consent');

        // role / is_admin are never taken from a public form: new accounts are always tourists.
        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('panel'))->with('status', __('¡Bienvenido a la ruta!'));
    }

    public function showLogin()
    {
        return view('account.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => __('Credenciales incorrectas.')]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        return redirect()->intended($user->canManageEvents() ? route('manage.dashboard') : route('panel'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
