<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class TypeCompanyRequest extends FormRequest
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
            'name' => 'required|string',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'logo.image'    => 'El archivo debe ser una imagen.',
            'logo.mimes'    => 'El logo debe ser PNG, JPG, JPEG.',
            'logo.max'      => 'El logo no puede superar los 10MB.',
        ];
    }

    #[Override]
    public function attributes()
    {
        return [
            'name' => "nombre"
        ];
    }
}
