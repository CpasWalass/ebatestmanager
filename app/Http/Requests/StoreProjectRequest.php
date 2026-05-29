<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['iat', 'uat'])],
            'status' => ['required', Rule::in(['planning', 'in_progress', 'completed', 'on_hold'])],
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
            'client_id.required' => 'Le client est obligatoire',
            'client_id.exists' => 'Le client sélectionné n\'existe pas',
            'name.required' => 'Le nom du projet est obligatoire',
            'type.required' => 'Le type de projet est obligatoire',
            'type.in' => 'Le type de projet doit être IAT ou UAT',
            'status.required' => 'Le statut est obligatoire',
            'status.in' => 'Le statut est invalide',
            'end_date.after_or_equal' => 'La date de fin doit être après ou égale à la date de début',
        ];
    }
}
