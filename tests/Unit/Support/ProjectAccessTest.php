<?php

namespace Tests\Unit\Support;

use App\Models\Project;
use App\Models\TestCaseAssignment;
use App\Models\TestCaseTemplate;
use App\Models\User;
use App\Support\ProjectAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_assigned_to_project_returns_true_when_assignment_exists(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        TestCaseAssignment::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);

        $this->assertTrue(ProjectAccess::isAssignedToProject($user, $project->id));
    }

    public function test_is_assigned_to_project_returns_false_when_no_assignment(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $this->assertFalse(ProjectAccess::isAssignedToProject($user, $project->id));
    }

    public function test_is_assigned_to_template_returns_true_when_direct_assignment(): void
    {
        $user = User::factory()->create();
        $template = TestCaseTemplate::factory()->create();

        TestCaseAssignment::factory()->create([
            'user_id' => $user->id,
            'template_id' => $template->id,
        ]);

        $this->assertTrue(ProjectAccess::isAssignedToTemplate($user, $template));
    }

    public function test_is_assigned_to_template_returns_true_when_project_assignment(): void
    {
        $user = User::factory()->create();
        $template = TestCaseTemplate::factory()->create();

        TestCaseAssignment::factory()->create([
            'user_id' => $user->id,
            'project_id' => $template->project_id,
        ]);

        $this->assertTrue(ProjectAccess::isAssignedToTemplate($user, $template));
    }

    public function test_is_assigned_to_template_returns_false_when_no_assignment(): void
    {
        $user = User::factory()->create();
        $template = TestCaseTemplate::factory()->create();

        $this->assertFalse(ProjectAccess::isAssignedToTemplate($user, $template));
    }

    public function test_is_developer_on_project_returns_true(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->developers()->attach($user);

        $this->assertTrue(ProjectAccess::isDeveloperOnProject($user, $project));
    }

    public function test_is_developer_on_project_returns_false(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $this->assertFalse(ProjectAccess::isDeveloperOnProject($user, $project));
    }
}
