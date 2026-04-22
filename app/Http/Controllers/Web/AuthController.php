<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    // ── Inscription ──────────────────────────────────────────

    public function showRegister()
    {
        if (Auth::check()) return redirect()->route('account.dashboard');
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            // CORRECTION #8 : 'name' supprimé — c'est un accessor calculé
            // depuis first_name + last_name, pas une colonne DB
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'password'   => Hash::make($data['password']),
        ]);

        Auth::login($user, true);

        try {
            Mail::to($user->email)->queue(new WelcomeMail($user));
        } catch (\Exception $e) {
            Log::warning('WelcomeMail failed for user ' . $user->id . ': ' . $e->getMessage());
        }

        return redirect()
            ->route('account.dashboard')
            ->with('success', 'Bienvenue sur DiscovTrip, ' . $data['first_name'] . ' !');
    }

    // ── Connexion ────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('account.dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ── CORRECTION #7 : rate limiting brute force ────────────
        // Clé unique par IP + email pour éviter les attaques ciblées
        $key = 'login:' . $request->ip() . ':' . $request->input('email');

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Trop de tentatives de connexion. Réessayez dans {$seconds} secondes.",
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Succès → réinitialiser le compteur
            RateLimiter::clear($key);
            $request->session()->regenerate();

            return redirect()
                ->intended(route('account.dashboard'))
                ->with('success', 'Bon retour, ' . Auth::user()->name . ' !');
        }

        // Échec → incrémenter le compteur (60s d'expiration)
        RateLimiter::hit($key, 60);

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Ces identifiants ne correspondent à aucun compte.']);
    }

    // ── Déconnexion ──────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Vous avez été déconnecté.');
    }

    // ── Mot de passe oublié ──────────────────────────────────

    public function showForgotPasswordForm()
    {
        if (Auth::check()) return redirect()->route('account.dashboard');
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // ── Réinitialisation du mot de passe ─────────────────────

    public function showResetPasswordForm(string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request()->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Mot de passe réinitialisé. Vous pouvez vous connecter.')
            : back()->withErrors(['email' => __($status)]);
    }
}