<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermissionTo('assign tests');
    }

    public function rules(): array
    {
        return [
            'test_case_id' => 'required|exists:test_cases,id',
            'user_id' => 'required|exists:users,id',
            'scope' => 'nullable|in:full_case,partial',
            'specific_fields' => 'nullable|array',
        ];
    }
}
