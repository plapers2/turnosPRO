<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Override;

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
        // Obtiene el ID según si la ruta pasa el modelo o solo el ID
        $userId = $this->route('user') instanceof \App\Models\User
            ? $this->route('user')->id
            : $this->route('user');

        $isEditing = !is_null($userId);

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'max:255',
                $isEditing
                    ? Rule::unique('users', 'email')->ignore($userId, 'id')
                    : Rule::unique('users', 'email'),
            ],
            // La imagen solo es obligatoria al crear
            'image'    => [$isEditing ? 'nullable' : 'required', 'image', 'mimes:png,jpg,jpeg', 'max:10240'],
            'phone'    => ['required', 'string', 'min:7', 'max:20', 'regex:/^\+?[\d\s\-\(\)]+$/'],
            'services'   => 'required|array',
            'services.*' => 'exists:services,id',
            'password'   => $isEditing
                ? ['nullable', 'string', 'confirmed', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()]
                : ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
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

    #[Override]
    public function attributes()
    {

        return [
            'password' => 'contraseña',
            'phone' => 'telefono',
            'services' => 'servicios',
            'image' => 'imagen',

        ];
    }
}
