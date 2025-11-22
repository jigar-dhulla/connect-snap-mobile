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
class Register extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:8')]
    public string $password = '';

    #[Validate('required|same:password')]
    public string $password_confirmation = '';

    public function register(ConnectSnapApi $api, TokenStorage $tokenStorage): void
    {
        $this->validate();

        try {
            $response = $api->register([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Store token securely (NativePHP SecureStorage or session fallback)
                $tokenStorage->setToken($data['token']);
                $tokenStorage->setUser($data['user']);

                $this->redirect(route('home'), navigate: true);
            } else {
                $errors = $response->json('errors', []);
                $message = $response->json('message', 'Registration failed.');

                if (! empty($errors)) {
                    throw ValidationException::withMessages($errors);
                }

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
        return view('livewire.auth.register');
    }
}