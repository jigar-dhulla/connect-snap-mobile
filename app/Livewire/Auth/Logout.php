<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Services\ConnectSnapApi;
use App\Services\TokenStorage;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Logout extends Component
{
    public function mount(ConnectSnapApi $api, TokenStorage $tokenStorage): void
    {
        $token = $tokenStorage->getToken();

        if ($token) {
            try {
                // Call API to invalidate token
                $api->withToken($token)->logout();
            } catch (\Exception $e) {
                // Continue with local logout even if API call fails
            }
        }

        // Clear local storage
        $tokenStorage->clear();

        // Redirect to login
        $this->redirect(route('login'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.logout');
    }
}
