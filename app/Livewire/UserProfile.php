<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserProfile extends Component
{
    use WithFileUploads;

    public $name;

    public $email;

    public $current_password;

    public $temporary_password;

    public $password;

    public $password_confirmation;

    public $avatar;

    public function mount()
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    public function updateProfile()
    {
        $this->email = strtolower(trim($this->email));

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->where(fn ($q) => $q->whereRaw('LOWER(email) = ?', [strtolower($this->email)]))
                    ->ignore(auth()->id()),
            ],
            'avatar' => 'nullable|image|max:1024',
        ]);

        $user = auth()->user();
        $user->name = $this->name;
        $user->email = $this->email;

        if ($this->avatar) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $this->avatar->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        session()->flash('success', 'Profil mis à jour avec succès.');
        $this->dispatch('profile-updated');
    }

    public function updatePassword()
    {
        $user = auth()->user();

        if ($user->must_change_password) {
            $rules = [
                'temporary_password' => ['required'],
                'password' => ['required', 'min:8', 'confirmed'],
            ];
            $messages = [
                'password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
                'temporary_password.required' => 'Veuillez saisir votre mot de passe temporaire.',
            ];
            $this->validate($rules, $messages);

            if (blank($this->temporary_password) || ! Hash::check($this->temporary_password, $user->temporary_password_hash)) {
                $this->addError('temporary_password', 'Le mot de passe temporaire est invalide.');

                return;
            }
        } else {
            $rules = [
                'current_password' => ['required'],
                'password' => ['required', 'min:8', 'confirmed'],
            ];
            $messages = [
                'password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
                'current_password.required' => 'Le mot de passe actuel est requis.',
            ];
            $this->validate($rules, $messages);

            if (! Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'Le mot de passe actuel est incorrect.');

                return;
            }
        }

        $wasMustChange = $user->must_change_password;

        $user->password = Hash::make($this->password);
        $user->must_change_password = false;
        $user->temporary_password_hash = null;
        $user->save();

        if ($wasMustChange) {
            session()->flash('success', 'Mot de passe mis à jour avec succès. Bienvenue !');
            if ($user->hasRole('tester')) {
                return $this->redirectRoute('testeur.dashboard');
            }
            if ($user->hasRole('developer')) {
                return $this->redirectRoute('developpeur.dashboard');
            }
            if ($user->hasRole('client')) {
                return $this->redirectRoute('client.dashboard');
            }

            return $this->redirectRoute('dashboard');
        }

        $this->reset(['current_password', 'temporary_password', 'password', 'password_confirmation']);
        session()->flash('success_password', 'Mot de passe mis à jour avec succès.');
    }

    public function render()
    {
        return view('livewire.user-profile')->layout('layouts.app');
    }
}
