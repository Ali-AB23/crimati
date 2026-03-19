<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' =>['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginInput = $this->input('username'); // Peut être le username OU le matricule
        $password = $this->input('password');

        // 1. RECHERCHE MULTI-CHAMPS (Users + Employees)
        $user = User::where('username', $loginInput)
            ->orWhereHas('employee', function ($query) use ($loginInput) {
                $query->where('matricule', $loginInput);
            })->first();

        // 2. L'utilisateur n'existe pas du tout
        if (! $user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'), // "Identifiants incorrects"
            ]);
        }

        // =========================================================
        // 🚀 SÉCURITÉ MÉTIER CONSERVÉE : Vérification du statut Actif/Inactif
        // =========================================================
        if (! $user->active) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => "Votre compte a été désactivé. Veuillez contacter l'administrateur IT.",
            ]);
        }

        // 3. VÉRIFICATION DU MOT DE PASSE
        if (! Hash::check($password, $user->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'), // Ne jamais dire si c'est le mdp ou l'identifiant qui est faux (Sécurité)
            ]);
        }

        // 4. CONNEXION RÉUSSIE
        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            // CORRECTION DU BUG BREEZE : On remplace 'email' par 'username'
            'username' => trans('auth.throttle',[
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('username')).'|'.$this->ip());
    }
}