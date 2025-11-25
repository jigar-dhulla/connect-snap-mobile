<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\ConnectSnapApi;
use App\Services\TokenStorage;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Mobile\Facades\Dialog;

#[Layout('components.layouts.guest')]
class PublicProfile extends Component
{
    public string $hash;

    public ?array $profile = null;

    public bool $loading = true;

    public bool $isLoggedIn = false;

    public bool $saving = false;

    public function mount(string $hash, ConnectSnapApi $api, TokenStorage $tokenStorage): void
    {
        $this->hash = $hash;
        $this->isLoggedIn = $tokenStorage->hasToken();

        $this->loadProfile($api);
    }

    private function loadProfile(ConnectSnapApi $api): void
    {
        try {
            $response = $api->getPublicProfile($this->hash);

            if ($response->successful()) {
                $this->profile = $response->json('data');
            } elseif ($response->status() === 404) {
                Dialog::toast('Profile not found.');
            } else {
                Dialog::toast('Failed to load profile.');
            }
        } catch (\Exception $e) {
            Dialog::toast('Unable to connect to server.');
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Save this profile as a connection (requires login).
     */
    public function saveConnection(ConnectSnapApi $api, TokenStorage $tokenStorage): void
    {
        if (! $this->isLoggedIn) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $this->saving = true;

        $token = $tokenStorage->getToken();

        try {
            $response = $api->withToken($token)->scanConnection($this->hash);

            if ($response->successful()) {
                Dialog::toast('Connection saved!');
            } else {
                $message = $response->json('message', 'Failed to save connection.');

                if ($response->status() === 409) {
                    $message = 'Already connected.';
                } elseif ($response->status() === 422) {
                    $message = 'Cannot connect with yourself.';
                }

                Dialog::toast($message);
            }
        } catch (\Exception $e) {
            Dialog::toast('Unable to save connection.');
        } finally {
            $this->saving = false;
        }
    }

    public function render(): View
    {
        return view('livewire.public-profile');
    }
}
