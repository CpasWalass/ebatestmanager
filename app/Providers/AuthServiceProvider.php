<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Client;
use App\Models\Project;
use App\Models\TestCase;
use App\Models\TestCaseTemplate;
use App\Policies\ClientPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TestCasePolicy;
use App\Policies\TestCaseTemplatePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Client::class => ClientPolicy::class,
        Project::class => ProjectPolicy::class,
        TestCaseTemplate::class => TestCaseTemplatePolicy::class,
        TestCase::class => TestCasePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
