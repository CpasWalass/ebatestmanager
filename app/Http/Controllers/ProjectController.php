<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::with(['client', 'creator'])
            ->paginate(15);

        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $this->authorize('create', Project::class);

        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $project = Project::create($validated);

        return response()->json([
            'message' => 'Projet créé avec succès',
            'data' => $project->load(['client', 'creator']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json($project->load(['client', 'creator']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        return response()->json([
            'message' => 'Projet mis à jour avec succès',
            'data' => $project->load(['client', 'creator']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json([
            'message' => 'Projet supprimé avec succès',
        ]);
    }

    /**
     * Get all test cases for a project
     */
    public function testCases(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $testCases = $project->testCases()->paginate(15);

        return response()->json($testCases);
    }

    /**
     * Get all templates for a project
     */
    public function templates(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $templates = $project->templates()->paginate(15);

        return response()->json($templates);
    }

    /**
     * Get all projects for a client
     */
    public function byClient(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        $projects = $client->projects()->paginate(15);

        return response()->json($projects);
    }
}
