<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestCaseTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage testcases');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'fields' => 'nullable|array',
            'fields.*.name' => 'required|string|max:100|distinct',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,textarea,email,date,number,select',
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array',
            'fields.*.options.*' => 'string|max:255',
            'fields.*.max_length' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'fields.*.name.distinct' => 'Les noms des champs doivent être uniques',
        ];
    }
}
