<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        $customerId = $this->route('customer')?->id;

        return [
            'name'  => 'required|string',
            'email' => [
                'required',
                'email',
                \Illuminate\Validation\Rule::unique('customers')->ignore($customerId),
                \Illuminate\Validation\Rule::unique('users', 'email'),
            ],
            'phone'      => 'required|string',
            'company_id' => 'required|exists:companies,id',
        ];
    }
    public function messages(): array
    {
        return [
            'email.unique' => 'Este correo electrónico ya está en uso.',
        ];
    }
}
