<?php

namespace App\Http\Controllers;

use App\Models\TestCaseTemplate;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTestCaseTemplateRequest;
use App\Http\Requests\UpdateTestCaseTemplateRequest;
use App\Services\TestCaseFieldService;

class TestCaseTemplateController extends Controller
{
    protected TestCaseFieldService $fieldService;

    public function __construct(TestCaseFieldService $fieldService)
    {
        $this->fieldService = $fieldService;
    }

    /**
     * Display a listing of templates for a project
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $templates = $project->templates()->paginate(15);

        return response()->json($templates);
    }

    /**
     * Store a newly created template
     */
    public function store(StoreTestCaseTemplateRequest $request, Project $project): JsonResponse
    {
        $this->authorize('manage testcases', 'App\Models\TestCase');

        $validated = $request->validated();
        $validated['project_id'] = $project->id;
        $validated['fields'] = $validated['fields'] ?? TestCaseTemplate::defaultFields();

        $template = TestCaseTemplate::create($validated);

        return response()->json([
            'message' => 'Template créé avec succès',
            'data' => $template,
        ], 201);
    }

    /**
     * Display the specified template
     */
    public function show(Project $project, TestCaseTemplate $template): JsonResponse
    {
        $this->authorize('view', $project);
        
        if ($template->project_id !== $project->id) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        return response()->json($template);
    }

    /**
     * Update the specified template
     */
    public function update(UpdateTestCaseTemplateRequest $request, Project $project, TestCaseTemplate $template): JsonResponse
    {
        $this->authorize('manage testcases', 'App\Models\TestCase');

        if ($template->project_id !== $project->id) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        $validated = $request->validated();
        $template->update($validated);

        return response()->json([
            'message' => 'Template mis à jour avec succès',
            'data' => $template,
        ]);
    }

    /**
     * Remove the specified template
     */
    public function destroy(Project $project, TestCaseTemplate $template): JsonResponse
    {
        $this->authorize('manage testcases', 'App\Models\TestCase');

        if ($template->project_id !== $project->id) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        // Prevent deletion if template has test cases
        if ($template->testCases()->count() > 0) {
            return response()->json([
                'error' => 'Impossible de supprimer ce template. Des cas de test l\'utilisent.',
            ], 409);
        }

        $template->delete();

        return response()->json([
            'message' => 'Template supprimé avec succès',
        ]);
    }

    /**
     * Get default fields for a new template
     */
    public function defaultFields(): JsonResponse
    {
        return response()->json([
            'fields' => $this->fieldService->getDefaultFields(),
        ]);
    }

    /**
     * Get field options for a select field
     */
    public function fieldOptions(Project $project, TestCaseTemplate $template, string $fieldName): JsonResponse
    {
        $this->authorize('view', $project);

        if ($template->project_id !== $project->id) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        $options = $this->fieldService->getFieldOptions($template, $fieldName);

        return response()->json([
            'field_name' => $fieldName,
            'options' => $options,
        ]);
    }
}
