<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Email Address',
        ];
    }

    public function verifyEmail(): bool
    {
        $user = \App\Models\User::where('email', $this->input('email'))->exists();

        return $user;
    }

    public function validateEmail()
    {
        $validated = $this->validated();

        if (! $this->verifyEmail()) {
            $this->merge(['email' => null]);
            $this->after(function ($validator) {
                $validator->errors()->add('email', 'No account found with this email address');
            });
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'No account found with this email address',
            ]);
        }

        return $validated;
    }
}
