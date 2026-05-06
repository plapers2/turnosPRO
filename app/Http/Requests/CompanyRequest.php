<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
            'name'            => 'required|string',
            'logo'            => ($this->isMethod('POST') ? 'required' : 'nullable') . '|image|mimes:png,jpg,jpeg,gif|max:10240',
            'email'           => 'required|string',
            'address'         => 'required|string',
            'phone'           => 'required|string|min:7|max:20|regex:/^\+?[\d\s\-\(\)]+$/',
            'type_company_id' => 'required',
        ];
    }
    public function messages(): array
    {
        return [
            'phone.regex' => 'El teléfono solo puede contener números, espacios, +, -, ( y ).',
            'phone.min'   => 'El teléfono debe tener al menos 7 dígitos.',
            'phone.max'   => 'El teléfono no puede tener más de 15 dígitos.',
        ];
    }
}
