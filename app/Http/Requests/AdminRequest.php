<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|min:8|max:20|regex:/^\+?[\d\s\-\(\)]+$/',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'El correo no tiene un formato válido.',
            'email.unique'   => 'Ya existe un usuario con ese correo.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.min'      => 'El teléfono debe tener al menos 8 dígitos.',
            'phone.max'      => 'El teléfono no puede tener más de 20 caracteres.',
            'phone.regex'    => 'El teléfono solo puede contener números, +, -, espacios y paréntesis.',
        ];
    }
}
