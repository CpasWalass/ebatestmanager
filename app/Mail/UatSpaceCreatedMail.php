<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Project;
use App\Models\User;

class UatSpaceCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Project $project,
        public User $user,
        public string $password,
        public string $loginLink
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation à l\'espace de recette (UAT) - ' . $this->project->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.uat-space-created',
            with: [
                'projectName' => $this->project->name,
                'clientName'  => $this->user->name,
                'email'       => $this->user->email,
                'password'    => $this->password,
                'url'         => $this->loginLink,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
