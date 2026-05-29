<?php

namespace App\Http\Controllers;

use App\Models\TestCase;
use App\Models\Project;
use App\Models\TestCaseTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTestCaseRequest;
use App\Http\Requests\UpdateTestCaseRequest;
use App\Services\TestCaseFieldService;

class TestCaseController extends Controller
{
    protected TestCaseFieldService $fieldService;

    public function __construct(TestCaseFieldService $fieldService)
    {
        $this->fieldService = $fieldService;
    }

    /**
     * Display a listing of test cases for a project
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $testCases = $project->testCases()
            ->with('template')
            ->paginate(15);

        return response()->json($testCases);
    }

    /**
     * Store a newly created test case
     */
    public function store(StoreTestCaseRequest $request, Project $project, TestCaseTemplate $template): JsonResponse
    {
        $this->authorize('manage testcases', 'App\Models\TestCase');

        if ($template->project_id !== $project->id) {
            return response()->json(['error' => 'Template not found for this project'], 404);
        }

        $validated = $request->validated();

        // Validate data against template fields
        try {
            $testCase = $this->fieldService->createTestCase($template, $validated['data'], $project->id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Cas de test créé avec succès',
            'data' => $testCase->load('template'),
        ], 201);
    }

    /**
     * Display the specified test case
     */
    public function show(Project $project, TestCase $testCase): JsonResponse
    {
        $this->authorize('view', $project);

        if ($testCase->project_id !== $project->id) {
            return response()->json(['error' => 'Test case not found'], 404);
        }

        $formatted = $this->fieldService->formatTestCaseData($testCase);

        return response()->json([
            'id' => $testCase->id,
            'template_id' => $testCase->template_id,
            'template' => $testCase->template,
            'fields' => $formatted,
            'created_at' => $testCase->created_at,
            'updated_at' => $testCase->updated_at,
        ]);
    }

    /**
     * Update the specified test case
     */
    public function update(UpdateTestCaseRequest $request, Project $project, TestCase $testCase): JsonResponse
    {
        $this->authorize('manage testcases', 'App\Models\TestCase');

        if ($testCase->project_id !== $project->id) {
            return response()->json(['error' => 'Test case not found'], 404);
        }

        $validated = $request->validated();

        // Update test case data
        try {
            $this->fieldService->updateTestCase($testCase, $validated['data']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Cas de test mis à jour avec succès',
            'data' => $testCase->load('template'),
        ]);
    }

    /**
     * Remove the specified test case
     */
    public function destroy(Project $project, TestCase $testCase): JsonResponse
    {
        $this->authorize('manage testcases', 'App\Models\TestCase');

        if ($testCase->project_id !== $project->id) {
            return response()->json(['error' => 'Test case not found'], 404);
        }

        $testCase->delete();

        return response()->json([
            'message' => 'Cas de test supprimé avec succès',
        ]);
    }

    /**
     * Get test cases by template
     */
    public function byTemplate(Project $project, TestCaseTemplate $template): JsonResponse
    {
        $this->authorize('view', $project);

        if ($template->project_id !== $project->id) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        $testCases = $template->testCases()->paginate(15);

        return response()->json($testCases);
    }
}
