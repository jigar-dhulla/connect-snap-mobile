<?php

declare(strict_types=1);

namespace App\Services;

use Native\Mobile\Facades\SecureStorage;

class TokenStorage
{
    private const TOKEN_KEY = 'api_token';

    private const USER_KEY = 'user';

    /**
     * Check if running in NativePHP environment.
     */
    protected function isNative(): bool
    {
        return function_exists('nativephp_secure_get');
    }

    /**
     * Store the API token.
     */
    public function setToken(?string $token): bool
    {
        if ($this->isNative()) {
            return SecureStorage::set(self::TOKEN_KEY, $token);
        }

        session([self::TOKEN_KEY => $token]);

        return true;
    }

    /**
     * Retrieve the API token.
     */
    public function getToken(): ?string
    {
        if ($this->isNative()) {
            return SecureStorage::get(self::TOKEN_KEY);
        }

        return session(self::TOKEN_KEY);
    }

    /**
     * Delete the API token.
     */
    public function deleteToken(): bool
    {
        if ($this->isNative()) {
            return SecureStorage::delete(self::TOKEN_KEY);
        }

        session()->forget(self::TOKEN_KEY);

        return true;
    }

    /**
     * Check if a token exists.
     */
    public function hasToken(): bool
    {
        return $this->getToken() !== null;
    }

    /**
     * Store user data.
     */
    public function setUser(?array $user): void
    {
        session([self::USER_KEY => $user]);
    }

    /**
     * Retrieve user data.
     */
    public function getUser(): ?array
    {
        return session(self::USER_KEY);
    }

    /**
     * Clear all auth data.
     */
    public function clear(): void
    {
        $this->deleteToken();
        session()->forget(self::USER_KEY);
    }
}