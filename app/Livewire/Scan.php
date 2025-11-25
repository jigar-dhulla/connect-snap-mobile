<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\ConnectSnapApi;
use App\Services\TokenStorage;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Mobile\Events\Scanner\CodeScanned;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\Scanner;

#[Layout('components.layouts.app')]
class Scan extends Component
{
    public ?string $scannedData = null;

    public ?array $scannedProfile = null;

    public bool $processing = false;

    /**
     * Open the native QR scanner.
     */
    public function openScanner(): void
    {
        Scanner::make()
            ->prompt('Scan a ConnectSnap QR code')
            ->formats(['qr_code'])
            ->continuous(false);
    }

    /**
     * Handle scanned QR code from native scanner.
     */
    #[On('native:'.CodeScanned::class)]
    public function handleScanned(string $data, string $format): void
    {
        $this->scannedData = $data;
        $this->processQrCode($data);
    }

    /**
     * Process the scanned QR code.
     */
    public function processQrCode(string $qrData): void
    {
        $this->processing = true;

        // Extract hash from QR code format: connectsnap://u/{hash}
        $hash = $this->extractHashFromQrCode($qrData);

        if (! $hash) {
            Dialog::toast('Invalid QR code format. Please scan a ConnectSnap QR code.');
            $this->processing = false;

            return;
        }

        $tokenStorage = app(TokenStorage::class);
        $token = $tokenStorage->getToken();

        if (! $token) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $api = app(ConnectSnapApi::class)->withToken($token);

        try {
            $response = $api->scanConnection($hash);

            if ($response->successful()) {
                $data = $response->json('data');
                $this->scannedProfile = $data;
                Dialog::toast('Connection added successfully!');
            } else {
                $message = $response->json('message', 'Failed to add connection.');

                // Handle specific error cases
                if ($response->status() === 409) {
                    $message = 'You are already connected with this person.';
                } elseif ($response->status() === 404) {
                    $message = 'Profile not found.';
                } elseif ($response->status() === 422) {
                    $message = 'You cannot scan your own QR code.';
                }

                Dialog::toast($message);
            }
        } catch (\Exception $e) {
            Dialog::toast('Unable to connect to server. Please try again.');
        } finally {
            $this->processing = false;
        }
    }

    /**
     * Reset to initial state for another scan.
     */
    public function scanAgain(): void
    {
        $this->scannedData = null;
        $this->scannedProfile = null;
        $this->processing = false;

        $this->openScanner();
    }

    /**
     * Clear result and go back to scan prompt.
     */
    public function clearResult(): void
    {
        $this->scannedData = null;
        $this->scannedProfile = null;
    }

    /**
     * Extract hash from QR code data.
     * Expected format: connectsnap://u/{hash}
     */
    private function extractHashFromQrCode(string $qrData): ?string
    {
        // Try connectsnap:// protocol
        if (preg_match('/^connectsnap:\/\/u\/([a-zA-Z0-9]+)$/', $qrData, $matches)) {
            return $matches[1];
        }

        // Also accept raw hash (for flexibility)
        if (preg_match('/^[a-zA-Z0-9]{20,50}$/', $qrData)) {
            return $qrData;
        }

        return null;
    }

    public function render(): View
    {
        return view('livewire.scan');
    }
}
