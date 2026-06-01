<?php

namespace App\Http\Controllers;

use App\Models\TestCase;
use App\Models\TestCaseAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreTestAssignmentRequest;

class TestAssignmentController extends Controller
{
    public function store(StoreTestAssignmentRequest $request): JsonResponse
    {
        $this->authorize('assign tests', TestCaseAssignment::class);

        $data = $request->validated();

        // Verify test case exists and belongs to project/tenant
        $testCase = TestCase::findOrFail($data['test_case_id']);

        $assignment = TestCaseAssignment::create([
            'test_case_id' => $testCase->id,
            'user_id' => $data['user_id'],
            'assigned_by' => auth()->id(),
            'scope' => $data['scope'] ?? 'full_case',
            'specific_fields' => $data['specific_fields'] ?? null,
        ]);

        return response()->json(['message' => 'Assignation créée', 'data' => $assignment], 201);
    }

    public function destroy(TestCaseAssignment $assignment): JsonResponse
    {
        $this->authorize('assign tests', TestCaseAssignment::class);

        $assignment->delete();

        return response()->json(['message' => 'Assignation supprimée']);
    }
}
