<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Override;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        // Obtiene el ID
        $userId = auth()->id();

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'min:8', 'max:20', 'regex:/^\+?[\d\s\-\(\)]+$/'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:10240'],
            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'id')
            ],
        ];

        if ($this->filled('new_password')) {
            $rules['current_password'] = ['required'];
            $rules['new_password']     = [
                'required',
                'confirmed',
                'confirmed',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'El nombre es obligatorio.',
            'name.max'             => 'El nombre no puede superar los 255 caracteres.',
            'phone.min'            => 'El teléfono debe tener al menos 8 dígitos.',
            'phone.required'       => 'El teléfono es obligatorio.',
            'phone.max'            => 'El teléfono no puede superar los 20 caracteres.',
            'phone.regex'          => 'El teléfono solo puede contener números, espacios, +, -, ( y ).',
            'image.image'          => 'El archivo debe ser una imagen.',
            'image.mimes'          => 'La imagen debe ser PNG o JPG.',
            'image.max'            => 'La imagen no puede superar los 10MB.',
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
            'new_password.required'     => 'La nueva contraseña es obligatoria.',
            'new_password.confirmed'    => 'Las contraseñas no coinciden.',
        ];
    }

    #[Override]
    public function attributes()
    {
        return [
            'new_password' => "nueva contraseña"
        ];
    }
}
