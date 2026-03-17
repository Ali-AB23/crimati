<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

        // =========================================================
        // 🚀 SÉCURITÉ MÉTIER : Vérification du statut Actif/Inactif
        // =========================================================
        // On cherche l'utilisateur dans la base de données
        $user = User::where('username', $this->input('username'))->first();

        // S'il existe MAIS qu'il est inactif (active == false ou 0)
        if ($user && ! $user->active) {
            // On compte quand même ça comme une tentative échouée pour la sécurité anti-spam
            RateLimiter::hit($this->throttleKey());

            // On rejette la connexion avec un message très explicite pour l'employé
            throw ValidationException::withMessages([
                'username' => "Votre compte a été désactivé. Veuillez contacter l'administrateur IT.",
            ]);
        }

        // Si l'utilisateur est actif, on passe à la vérification standard du mot de passe
        if (! Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

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