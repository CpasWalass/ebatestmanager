<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ClientManager extends Component
{
    public string $search = '';

    public bool $showModal = false;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public string $city = '';

    public string $country = "Côte d'Ivoire";

    #[Computed]
    public function clients()
    {
        return Client::query()
            ->when($this->search, fn ($q) => $q
                ->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
            )
            ->withCount('projects')
            ->orderBy('name')
            ->get();
    }

    public function openNewModal(): void
    {
        $this->authorize('create', Client::class);

        $this->reset(['name', 'email', 'phone', 'address', 'city']);
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize('create', Client::class);

        // L'email est NOT NULL + UNIQUE en base (voir migration clients) ; avant,
        // la règle 'nullable|email' laissait passer un formulaire vide côté
        // validation Livewire, qui échouait ensuite avec une erreur 500 brute au
        // niveau de la base de données au lieu d'un message clair à l'utilisateur.
        $this->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255|unique:clients,email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        Client::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        $this->showModal = false;
        $this->reset(['name', 'email', 'phone', 'address', 'city', 'country']);
        session()->flash('success', 'Client créé avec succès.');
    }

    public function deleteClient(int $clientId): void
    {
        $client = Client::withCount('projects')->find($clientId);
        $this->authorize('delete', $client);

        if ($client && $client->projects_count === 0) {
            $client->delete();
            session()->flash('success', 'Client supprimé.');
        } else {
            session()->flash('error', 'Impossible de supprimer un client avec des projets actifs.');
        }
    }

    public function render()
    {
        return view('livewire.client-manager');
    }
}
