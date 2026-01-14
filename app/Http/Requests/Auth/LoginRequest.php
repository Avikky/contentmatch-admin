<?php

namespace App\Http\Requests\Auth;

use App\Facades\LoginTrail;
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
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

        $this->defaultLogin();

        RateLimiter::clear($this->throttleKey());
    }

    public function defaultLogin()
    {
        if (! $this->verifyCredentials($this->input('username'), $this->input('password'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        if (Auth::check()) {
            if (Auth::user()->is_locked) {
                Auth::logout();
                // Log failed login attempt
                LoginTrail::logFailedLogin(
                    $this->input('username', 'unknown'),
                    'Authentication failed: Unable to log in user account is locked.'
                );
                throw ValidationException::withMessages([
                    'username' => 'Your account is locked. Please reset your password.',
                ]);
            }
        }
    }

    public function verifyCredentials($username, $password)
    {
        try {

            $user = User::where('email', $username)->orWhere('user_name', $username)->orWhere('pin', $username)->first();

            // check if password matches
            if ($user && password_verify($password, $user->password)) {
                Auth::login($user, $this->boolean('remember'));

                return 1; // Successful authentication
            }

        } catch (\Throwable $th) {
            return 0;
        }
    }

    public function getUserDetails($username): ?User
    {
        try {
            if ($username == null) {
                return null;
            }

            $query = User::query();

            if ($query->where('email', $username)->exists()) {
                $usernameType = 'email';
            } elseif ($query->where('user_name', $username)->exists()) {
                $usernameType = 'username';
            } elseif ($query->where('pin', $username)->exists()) {
                $usernameType = 'pin';
            } else {
                $usernameType = 'unknown';
            }

            switch ($usernameType) {
                case 'email':
                    $users_details = User::where('email', $username)->first();
                    break;
                case 'username':
                    $users_details = User::where('user_name', $username)->first();
                    break;
                case 'pin':
                    $users_details = User::where('pin', $username)->first();
                    break;
                default:
                    $users_details = User::where('email', $username)->orWhere('user_name', $username)->orWhere('pin', $username)->first();
            }

            if ($users_details) {
                $users_details['username_type'] = $usernameType;

                return $users_details;
            } else {
                return null;
            }

        } catch (\Throwable $th) {
            throw $th->getMessage();
        }
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
            'username' => trans('auth.throttle', [
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
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }
}
