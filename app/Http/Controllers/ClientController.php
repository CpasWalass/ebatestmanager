<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Client::class);

        $clients = Client::paginate(15);
        return response()->json($clients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        $this->authorize('create', Client::class);

        $client = Client::create($request->validated());

        return response()->json([
            'message' => 'Client créé avec succès',
            'data' => $client,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): JsonResponse
    {
        $this->authorize('view', $client);

        return response()->json($client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return response()->json([
            'message' => 'Client mis à jour avec succès',
            'data' => $client,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): JsonResponse
    {
        $this->authorize('delete', $client);

        $client->delete();

        return response()->json([
            'message' => 'Client supprimé avec succès',
        ]);
    }

    /**
     * Get all projects for a client
     */
    public function projects(Client $client): JsonResponse
    {
        $this->authorize('view', $client) ;

        $projects = $client->projects()->paginate(15);

        return response()->json($projects);
    }
}
