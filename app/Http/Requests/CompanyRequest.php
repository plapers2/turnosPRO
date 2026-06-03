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
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'address'         => 'required|string|max:255',
            'phone'           => 'required|string|min:7|max:20|regex:/^\+?[\d\s\-\(\)]+$/',
            'type_company_id'   => 'required_if:type_type,existing|nullable|exists:type_companies,id',
            'type_type'         => 'required|in:existing,new',
            'type_company_name' => 'required_if:type_type,new|nullable|string|max:255',
            'logo'            => 'nullable|image|mimes:png,jpg,jpeg|max:10240',
            'admin_type'      => 'sometimes|required|in:existing,new',
            'admin_id'        => 'required_if:admin_type,existing|nullable|exists:users,id',
            'admin_name'      => 'required_if:admin_type,new|nullable|string|max:255',
            'admin_email'     => 'required_if:admin_type,new|nullable|email|unique:users,email',
        ];
    }
    public function messages(): array
    {
        return [
            // Empresa
            'name.required'            => 'El nombre de la empresa es obligatorio.',
            'email.required'           => 'El correo electrónico es obligatorio.',
            'email.email'              => 'El correo electrónico no tiene un formato válido.',
            'address.required'         => 'La dirección es obligatoria.',
            'phone.required'           => 'El teléfono es obligatorio.',
            'phone.regex'              => 'El teléfono solo puede contener números, +, -, espacios y paréntesis.',
            'phone.min'                => 'El teléfono debe tener al menos 7 dígitos.',
            'phone.max'                => 'El teléfono no puede tener más de 20 dígitos.',
            'type_company_id.required' => 'Debes seleccionar un tipo de empresa.',
            'type_type.required'         => 'Debes indicar si seleccionas o creas un tipo de empresa.',
            'type_company_id.required_if' => 'Debes seleccionar un tipo de empresa existente.',
            'type_company_name.required_if' => 'El nombre del nuevo tipo de empresa es obligatorio.',
            'type_company_id.exists'   => 'El tipo de empresa seleccionado no es válido.',
            'logo.image'               => 'El archivo debe ser una imagen.',
            'logo.mimes'               => 'El logo debe ser PNG, JPG o JPEG.',
            'logo.max'                 => 'El logo no puede superar los 10MB.',

            // Admin
            'admin_type.required'      => 'Debes indicar si asignas un admin existente o creas uno nuevo.',
            'admin_type.in'            => 'El tipo de administrador no es válido.',
            'admin_id.required_if'     => 'Debes seleccionar un administrador existente.',
            'admin_id.exists'          => 'El administrador seleccionado no existe.',
            'admin_name.required_if'   => 'El nombre del nuevo administrador es obligatorio.',
            'admin_email.required_if'  => 'El correo del nuevo administrador es obligatorio.',
            'admin_email.email'        => 'El correo del administrador no tiene un formato válido.',
            'admin_email.unique'       => 'Ya existe un usuario con ese correo.',
        ];
    }
}
