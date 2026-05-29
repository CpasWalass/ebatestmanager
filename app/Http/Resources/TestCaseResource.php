<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestCaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'template_id' => $this->template_id,
            'project_id' => $this->project_id,
            'data' => $this->data,
            'template' => new TestCaseTemplateResource($this->whenLoaded('template')),
            'assignments_count' => $this->testCaseAssignments()->count(),
            'tenant_id' => $this->tenant_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
