<?php

namespace App\Livewire\Auth;

use App\Models\Message;
use App\Models\User;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $email = '';
    public $status = '';
    public $errorMessage = '';

    protected $rules = [
        'email' => 'required|email'
    ];

    public function requestReset()
    {
        $this->validate();
        $this->errorMessage = '';
        $this->status = '';

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->errorMessage = "Email introuvable.";
            return;
        }

        // Find project managers
        $chefProjets = User::role('chef_project')->get();

        if ($chefProjets->isEmpty()) {
            $this->errorMessage = "Aucun chef de projet n'est disponible pour traiter votre demande.";
            return;
        }

        foreach ($chefProjets as $chef) {
            Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $chef->id,
                'content' => "L'utilisateur {$user->name} ({$user->email}) a oublié son mot de passe et demande une réinitialisation.",
                // type might not exist or be needed if it's default null, let's just omit type to be safe since it's just 'string' or null
                'tenant_id' => $user->tenant_id,
            ]);
        }

        $this->status = "Une notification a été envoyée au chef de projet. Vous recevrez bientôt un nouveau mot de passe.";
        $this->email = ''; // clear the input
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('layouts.auth');
    }
}
