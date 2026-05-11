<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'description' => 'required|string',
            'duration' => 'required',
            'price' => 'required',
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:10240'],
            'state' => 'required',
            'company_id' => 'required',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required'        => 'El nombre es obligatorio.',
            'name.max'             => 'El nombre no puede superar los 255 caracteres.',
            'image.image'          => 'El archivo debe ser una imagen.',
            'image.mimes'          => 'La imagen debe ser PNG o JPG.',
            'image.max'            => 'La imagen no puede superar los 10MB.',
        ];
    }
}
