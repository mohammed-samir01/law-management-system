<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => __('validation.required', ['attribute' => 'البريد الإلكتروني']),
            'password.required' => __('validation.required', ['attribute' => 'كلمة المرور']),
        ];
    }
}
