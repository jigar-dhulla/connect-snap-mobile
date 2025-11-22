<?php

namespace App\Livewire\Auth;

use App\Services\ConnectSnapApi;
use App\Services\TokenStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.guest')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:8')]
    public string $password = '';

    public bool $remember = false;

    public function login(ConnectSnapApi $api, TokenStorage $tokenStorage): void
    {
        $this->validate();

        try {
            $response = $api->login([
                'email' => $this->email,
                'password' => $this->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Store token securely (NativePHP SecureStorage or session fallback)
                $tokenStorage->setToken($data['token']);
                $tokenStorage->setUser($data['user']);

                $this->redirect(route('home'), navigate: true);
            } else {
                $message = $response->json('message', 'Invalid credentials.');

                throw ValidationException::withMessages([
                    'email' => $message,
                ]);
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'email' => 'Unable to connect to server. Please try again.',
            ]);
        }
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}