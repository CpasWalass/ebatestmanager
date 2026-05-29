<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage projects');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'sometimes|required|exists:clients,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['sometimes', 'required', Rule::in(['iat', 'uat'])],
            'status' => ['sometimes', 'required', Rule::in(['planning', 'in_progress', 'completed', 'on_hold'])],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'client_id.exists' => 'Le client sélectionné n\'existe pas',
            'type.in' => 'Le type de projet doit être IAT ou UAT',
            'status.in' => 'Le statut est invalide',
            'end_date.after_or_equal' => 'La date de fin doit être après ou égale à la date de début',
        ];
    }
}
