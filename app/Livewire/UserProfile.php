<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
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

        $rules = [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ];

        $this->validate($rules);

        $wasMustChange = $user->must_change_password;

        $user->password = Hash::make($this->password);
        $user->must_change_password = false;
        $user->temporary_password_hash = null;
        $user->save();

        if ($wasMustChange) {
            session()->flash('success', 'Mot de passe mis à jour avec succès. Bienvenue !');
            if ($user->hasRole('tester')) return $this->redirectRoute('testeur.dashboard');
            if ($user->hasRole('developer')) return $this->redirectRoute('developpeur.dashboard');
            if ($user->hasRole('client')) return $this->redirectRoute('client.dashboard');
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
