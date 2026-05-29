<?php

namespace App\Services;

use App\Models\TestCaseTemplate;
use App\Models\TestCase;
use Illuminate\Validation\ValidationException;

class TestCaseFieldService
{
    /**
     * Validate test case data against template fields
     */
    public function validateData(array $data, TestCaseTemplate $template): array
    {
        $rules = $this->buildValidationRules($template);
        
        return validator($data, $rules)->validate();
    }

    /**
     * Build validation rules from template fields
     */
    public function buildValidationRules(TestCaseTemplate $template): array
    {
        $rules = [];
        $fields = $template->fields ?? TestCaseTemplate::defaultFields();

        foreach ($fields as $field) {
            $rules[$field['name']] = $this->buildFieldRule($field);
        }

        return $rules;
    }

    /**
     * Build single field validation rule
     */
    private function buildFieldRule(array $field): string
    {
        $rule = [];

        // Required rule
        if ($field['required'] ?? false) {
            $rule[] = 'required';
        } else {
            $rule[] = 'nullable';
        }

        // Type-based rules
        switch ($field['type'] ?? 'text') {
            case 'email':
                $rule[] = 'email';
                break;
            case 'date':
                $rule[] = 'date';
                break;
            case 'number':
                $rule[] = 'numeric';
                break;
            case 'select':
                if (!empty($field['options'])) {
                    $options = implode(',', $field['options']);
                    $rule[] = "in:{$options}";
                }
                break;
            case 'textarea':
                $rule[] = 'string';
                break;
            case 'text':
            default:
                $rule[] = 'string';
                break;
        }

        // Max length
        if (!empty($field['max_length'])) {
            $rule[] = 'max:' . $field['max_length'];
        }

        return implode('|', $rule);
    }

    /**
     * Get default template fields
     */
    public function getDefaultFields(): array
    {
        return TestCaseTemplate::defaultFields();
    }

    /**
     * Create test case from template
     */
    public function createTestCase(TestCaseTemplate $template, array $data, int $projectId): TestCase
    {
        // Validate data
        $validatedData = $this->validateData($data, $template);

        // Create test case
        return TestCase::create([
            'template_id' => $template->id,
            'project_id' => $projectId,
            'data' => $validatedData,
        ]);
    }

    /**
     * Update test case data
     */
    public function updateTestCase(TestCase $testCase, array $data): TestCase
    {
        $template = $testCase->template;

        // Validate data
        $validatedData = $this->validateData($data, $template);

        // Update
        $testCase->update(['data' => $validatedData]);

        return $testCase;
    }

    /**
     * Get field options (for select fields)
     */
    public function getFieldOptions(TestCaseTemplate $template, string $fieldName): array
    {
        $fields = $template->fields ?? TestCaseTemplate::defaultFields();

        foreach ($fields as $field) {
            if ($field['name'] === $fieldName && $field['type'] === 'select') {
                return $field['options'] ?? [];
            }
        }

        return [];
    }

    /**
     * Format test case data for display
     */
    public function formatTestCaseData(TestCase $testCase): array
    {
        $template = $testCase->template;
        $fields = $template->fields ?? TestCaseTemplate::defaultFields();
        $data = $testCase->data ?? [];

        $formatted = [];
        foreach ($fields as $field) {
            $fieldName = $field['name'];
            $formatted[$fieldName] = [
                'label' => $field['label'],
                'type' => $field['type'],
                'value' => $data[$fieldName] ?? null,
                'required' => $field['required'] ?? false,
            ];
        }

        return $formatted;
    }
}
