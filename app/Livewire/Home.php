<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\ConnectSnapApi;
use App\Services\TokenStorage;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Home extends Component
{
    public ?array $profile = null;

    public ?string $qrCodeSvg = null;

    public ?string $qrCodeHash = null;

    public bool $loading = true;

    public ?string $error = null;

    public function mount(ConnectSnapApi $api, TokenStorage $tokenStorage): void
    {
        $token = $tokenStorage->getToken();

        if (! $token) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $this->loadProfileAndQrCode($api->withToken($token));
    }

    public function refresh(ConnectSnapApi $api, TokenStorage $tokenStorage): void
    {
        $this->loading = true;
        $this->error = null;

        $token = $tokenStorage->getToken();

        if ($token) {
            $this->loadProfileAndQrCode($api->withToken($token));
        }
    }

    private function loadProfileAndQrCode(ConnectSnapApi $api): void
    {
        try {
            // Fetch profile
            $profileResponse = $api->getProfile();

            if ($profileResponse->successful()) {
                $this->profile = $profileResponse->json('data');
            } else {
                $this->error = 'Failed to load profile.';
            }

            // Fetch QR code
            $qrResponse = $api->getQrCode();

            if ($qrResponse->successful()) {
                $qrData = $qrResponse->json('data');
                $encodedSvg = $qrData['qr_code_svg'] ?? null;
                $this->qrCodeSvg = $encodedSvg ? base64_decode($encodedSvg) : null;
                $this->qrCodeHash = $qrData['qr_code_hash'] ?? null;
            } else {
                $this->error = 'Failed to load QR code.';
            }
        } catch (\Exception $e) {
            $this->error = 'Unable to connect to server. Pull down to retry.';
        } finally {
            $this->loading = false;
        }
    }

    public function render(): View
    {
        return view('livewire.home');
    }
}
