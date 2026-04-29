<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateCustomerProfileRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];

        if ($this->filled('current_password') || $this->filled('new_password')) {
            $rules['current_password'] = ['required', function ($attr, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail('La contraseña actual no es correcta.');
                }
            }];
            $rules['new_password'] = 'required|string|min:8|confirmed';
        }

        return $rules;
    }
    public function messages(): array
    {
        return [
            'name.required'         => 'El nombre es obligatorio.',
            'new_password.min'      => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed' => 'La confirmación de contraseña no coincide.',
        ];
    }
}
