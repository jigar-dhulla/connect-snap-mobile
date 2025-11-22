<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class ConnectSnapApi
{
    protected string $baseUrl;

    protected ?string $token = null;

    public function __construct()
    {
        $this->baseUrl = config('services.connectsnap.url', 'http://localhost:8000');
    }

    /**
     * Set the authentication token for API requests.
     */
    public function withToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the configured HTTP client.
     */
    protected function client(): PendingRequest
    {
        $client = Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->asJson();

        if ($this->token) {
            $client->withToken($this->token);
        }

        return $client;
    }

    // ==========================================
    // Authentication Endpoints
    // ==========================================

    /**
     * Register a new user.
     *
     * @param  array{name: string, email: string, password: string, password_confirmation: string}  $data
     */
    public function register(array $data): Response
    {
        return $this->client()->post('/api/register', $data);
    }

    /**
     * Login a user.
     *
     * @param  array{email: string, password: string}  $data
     */
    public function login(array $data): Response
    {
        return $this->client()->post('/api/login', $data);
    }

    /**
     * Get the authenticated user.
     */
    public function user(): Response
    {
        return $this->client()->get('/api/user');
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(): Response
    {
        return $this->client()->post('/api/logout');
    }

    // ==========================================
    // Profile Endpoints
    // ==========================================

    /**
     * Get the authenticated user's profile.
     */
    public function getProfile(): Response
    {
        return $this->client()->get('/api/profile');
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param  array{name?: string, email?: string, phone?: string, company?: string, job_title?: string, bio?: string, social_url?: string}  $data
     */
    public function updateProfile(array $data): Response
    {
        return $this->client()->put('/api/profile', $data);
    }

    /**
     * Upload a profile photo.
     */
    public function uploadProfilePhoto(UploadedFile $photo): Response
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->withToken($this->token)
            ->attach('photo', $photo->getContent(), $photo->getClientOriginalName())
            ->post('/api/profile/photo');
    }

    /**
     * Get the authenticated user's QR code.
     */
    public function getQrCode(): Response
    {
        return $this->client()->get('/api/profile/qr-code');
    }

    /**
     * Get a public profile by QR code hash (no authentication required).
     */
    public function getPublicProfile(string $qrHash): Response
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->get("/api/u/{$qrHash}");
    }

    // ==========================================
    // Connections Endpoints
    // ==========================================

    /**
     * Scan a QR code to add a connection.
     */
    public function scanConnection(string $qrCodeHash): Response
    {
        return $this->client()->post('/api/connections/scan', [
            'qr_code_hash' => $qrCodeHash,
        ]);
    }

    /**
     * Get all connections for the authenticated user.
     *
     * @param  array{search?: string, page?: int, per_page?: int}  $params
     */
    public function getConnections(array $params = []): Response
    {
        return $this->client()->get('/api/connections', $params);
    }

    /**
     * Get a single connection by ID.
     */
    public function getConnection(int $id): Response
    {
        return $this->client()->get("/api/connections/{$id}");
    }

    /**
     * Update notes for a connection.
     */
    public function updateConnectionNotes(int $id, string $notes): Response
    {
        return $this->client()->put("/api/connections/{$id}/notes", [
            'notes' => $notes,
        ]);
    }

    /**
     * Delete a connection.
     */
    public function deleteConnection(int $id): Response
    {
        return $this->client()->delete("/api/connections/{$id}");
    }
}