<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class NativeEdge extends Component
{
    public string $title = 'ConnectSnap';

    public int $connectionsCount = 0;

    public function mount(): void
    {
        $this->refreshConnectionsCount();
    }

    #[On('connection-added')]
    #[On('connection-removed')]
    public function refreshConnectionsCount(): void
    {
        // TODO: Update with actual connections count from API
        $this->connectionsCount = 0;
    }

    public function render(): View
    {
        return view('livewire.native-edge');
    }
}
