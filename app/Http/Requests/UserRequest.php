<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
        $rules =
            [
                'name' => 'required|string',
                'email' => 'required|string',
                'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:10240'],
                'phone' => ['nullable', 'string', 'min:7', 'max:20', 'regex:/^\+?[\d\s\-\(\)]+$/'],
            ];
        if ($this->filled('new_password')) {
            $rules['current_password'] = ['required'];
            $rules['new_password']     = ['required', Password::min(8), 'confirmed'];
        }

        return $rules;
    }
    public function messages(): array
    {
        return [
            'name.required'        => 'El nombre es obligatorio.',
            'name.max'             => 'El nombre no puede superar los 255 caracteres.',
            'phone.min'            => 'El teléfono debe tener al menos 7 dígitos.',
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
}
