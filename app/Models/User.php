<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'email_verified_at', 'tenant_id', 'is_active', 'avatar', 'must_change_password', 'temporary_password_hash', 'failed_login_attempts', 'locked_until'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use BelongsToTenant, HasFactory, HasRoles, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'locked_until' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            if ($user->email) {
                $user->email = strtolower(trim($user->email));
            }
        });
    }

    /**
     * Cas de test assignés à cet utilisateur (testeur ou développeur).
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TestCaseAssignment::class);
    }

    /**
     * Exécutions de tests réalisées par cet utilisateur (testeur).
     */
    public function testExecutions(): HasMany
    {
        return $this->hasMany(TestExecution::class, 'tester_id');
    }

    /**
     * Projets créés par cet utilisateur (typiquement chef de projet).
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    /**
     * Projets sur lesquels cet utilisateur intervient comme développeur.
     */
    public function projectsAsDeveloper(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function isChefProjet(): bool
    {
        return $this->hasRole('chef_project');
    }

    public function isTesteur(): bool
    {
        return $this->hasRole('tester');
    }

    public function isDeveloppeur(): bool
    {
        return $this->hasRole('developer');
    }

    public function isClient(): bool
    {
        return $this->hasRole('client');
    }
}
