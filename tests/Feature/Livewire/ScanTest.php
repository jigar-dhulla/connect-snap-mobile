<?php

declare(strict_types=1);

use App\Livewire\Scan;
use App\Services\ConnectSnapApi;
use App\Services\TokenStorage;
use Illuminate\Http\Client\Response;
use Livewire\Livewire;
use Native\Mobile\Facades\Scanner;

use function Pest\Laravel\mock;

beforeEach(function () {
    // Mock TokenStorage to simulate logged-in user
    $this->tokenStorage = mock(TokenStorage::class);
    $this->tokenStorage->shouldReceive('getToken')->andReturn('test-token')->byDefault();

    // Mock ConnectSnapApi
    $this->api = mock(ConnectSnapApi::class);
    $this->api->shouldReceive('withToken')->andReturnSelf()->byDefault();
});

test('scan component renders initial state', function () {
    Livewire::test(Scan::class)
        ->assertSet('scannedData', null)
        ->assertSet('scannedProfile', null)
        ->assertSet('error', null)
        ->assertSet('success', null)
        ->assertSet('processing', false)
        ->assertSee('Scan QR Code')
        ->assertSee('Open Scanner');
});

test('extracts hash from connectsnap protocol format', function () {
    $profileData = [
        'id' => 1,
        'scanned_profile' => [
            'id' => 2,
            'name' => 'John Doe',
            'job_title' => 'Developer',
            'company' => 'Acme',
            'bio' => null,
            'profile_photo_url' => null,
            'social_url' => null,
        ],
        'notes' => null,
        'met_at' => '2025-11-22T09:16:56.000000Z',
    ];

    $response = mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->with('data')->andReturn($profileData);

    $this->api->shouldReceive('scanConnection')
        ->with('abc123xyz789')
        ->once()
        ->andReturn($response);

    Livewire::test(Scan::class)
        ->call('processQrCode', 'connectsnap://u/abc123xyz789')
        ->assertSet('success', 'Connection added successfully!')
        ->assertSet('scannedProfile', $profileData)
        ->assertSet('processing', false);
});

test('extracts hash from raw hash format', function () {
    $profileData = [
        'id' => 1,
        'scanned_profile' => ['id' => 2, 'name' => 'Jane Doe'],
        'notes' => null,
        'met_at' => '2025-11-22T09:16:56.000000Z',
    ];

    $response = mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->with('data')->andReturn($profileData);

    $this->api->shouldReceive('scanConnection')
        ->with('v8nDfMEL59EYGneaAG1vVgtM')
        ->once()
        ->andReturn($response);

    Livewire::test(Scan::class)
        ->call('processQrCode', 'v8nDfMEL59EYGneaAG1vVgtM')
        ->assertSet('success', 'Connection added successfully!');
});

test('shows error for invalid QR code format', function () {
    Livewire::test(Scan::class)
        ->call('processQrCode', 'invalid-qr-code')
        ->assertSet('error', 'Invalid QR code format. Please scan a ConnectSnap QR code.')
        ->assertSet('processing', false);
});

test('shows error for QR code with wrong protocol', function () {
    Livewire::test(Scan::class)
        ->call('processQrCode', 'https://example.com/u/abc123')
        ->assertSet('error', 'Invalid QR code format. Please scan a ConnectSnap QR code.');
});

test('shows error for too short hash', function () {
    Livewire::test(Scan::class)
        ->call('processQrCode', 'abc123')
        ->assertSet('error', 'Invalid QR code format. Please scan a ConnectSnap QR code.');
});

test('redirects to login when no token available', function () {
    $this->tokenStorage->shouldReceive('getToken')->andReturn(null);

    Livewire::test(Scan::class)
        ->call('processQrCode', 'connectsnap://u/abc123xyz789abcdef012')
        ->assertRedirect(route('login'));
});

test('handles already connected error (409)', function () {
    $response = mock(Response::class);
    $response->shouldReceive('successful')->andReturn(false);
    $response->shouldReceive('status')->andReturn(409);
    $response->shouldReceive('json')->with('message', 'Failed to add connection.')->andReturn('Conflict');

    $this->api->shouldReceive('scanConnection')->once()->andReturn($response);

    Livewire::test(Scan::class)
        ->call('processQrCode', 'connectsnap://u/abc123xyz789abcdef012')
        ->assertSet('error', 'You are already connected with this person.')
        ->assertSet('processing', false);
});

test('handles profile not found error (404)', function () {
    $response = mock(Response::class);
    $response->shouldReceive('successful')->andReturn(false);
    $response->shouldReceive('status')->andReturn(404);
    $response->shouldReceive('json')->with('message', 'Failed to add connection.')->andReturn('Not found');

    $this->api->shouldReceive('scanConnection')->once()->andReturn($response);

    Livewire::test(Scan::class)
        ->call('processQrCode', 'connectsnap://u/abc123xyz789abcdef012')
        ->assertSet('error', 'Profile not found.');
});

test('handles own QR code error (422)', function () {
    $response = mock(Response::class);
    $response->shouldReceive('successful')->andReturn(false);
    $response->shouldReceive('status')->andReturn(422);
    $response->shouldReceive('json')->with('message', 'Failed to add connection.')->andReturn('Cannot scan own');

    $this->api->shouldReceive('scanConnection')->once()->andReturn($response);

    Livewire::test(Scan::class)
        ->call('processQrCode', 'connectsnap://u/abc123xyz789abcdef012')
        ->assertSet('error', 'You cannot scan your own QR code.');
});

test('handles generic API error', function () {
    $response = mock(Response::class);
    $response->shouldReceive('successful')->andReturn(false);
    $response->shouldReceive('status')->andReturn(500);
    $response->shouldReceive('json')->with('message', 'Failed to add connection.')->andReturn('Server error');

    $this->api->shouldReceive('scanConnection')->once()->andReturn($response);

    Livewire::test(Scan::class)
        ->call('processQrCode', 'connectsnap://u/abc123xyz789abcdef012')
        ->assertSet('error', 'Server error');
});

test('handles connection exception', function () {
    $this->api->shouldReceive('scanConnection')
        ->once()
        ->andThrow(new \Exception('Network error'));

    Livewire::test(Scan::class)
        ->call('processQrCode', 'connectsnap://u/abc123xyz789abcdef012')
        ->assertSet('error', 'Unable to connect to server. Please try again.')
        ->assertSet('processing', false);
});

test('scanAgain resets state', function () {
    // Mock the PendingQrCodeScan that Scanner::make() returns
    $pendingScan = Mockery::mock(\Native\Mobile\PendingQrCodeScan::class);
    $pendingScan->shouldReceive('prompt')->andReturnSelf();
    $pendingScan->shouldReceive('formats')->andReturnSelf();
    $pendingScan->shouldReceive('continuous')->andReturnSelf();

    Scanner::shouldReceive('make')->andReturn($pendingScan);

    $profileData = [
        'id' => 1,
        'scanned_profile' => ['id' => 2, 'name' => 'Test'],
        'notes' => null,
        'met_at' => '2025-11-22T09:16:56.000000Z',
    ];

    $response = mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->with('data')->andReturn($profileData);

    $this->api->shouldReceive('scanConnection')->andReturn($response);

    Livewire::test(Scan::class)
        ->call('processQrCode', 'connectsnap://u/abc123xyz789abcdef012')
        ->assertSet('scannedProfile', $profileData)
        ->call('scanAgain')
        ->assertSet('scannedData', null)
        ->assertSet('scannedProfile', null)
        ->assertSet('error', null)
        ->assertSet('success', null)
        ->assertSet('processing', false);
});

test('clearResult resets state without opening scanner', function () {
    Livewire::test(Scan::class)
        ->set('scannedData', 'test-data')
        ->set('error', 'Some error')
        ->call('clearResult')
        ->assertSet('scannedData', null)
        ->assertSet('scannedProfile', null)
        ->assertSet('error', null)
        ->assertSet('success', null);
});

test('shows success view after successful scan', function () {
    $profileData = [
        'id' => 1,
        'scanned_profile' => [
            'id' => 2,
            'name' => 'John Doe',
            'job_title' => 'Developer',
            'company' => 'Acme',
            'bio' => null,
            'profile_photo_url' => null,
            'social_url' => null,
        ],
        'notes' => null,
        'met_at' => '2025-11-22T09:16:56.000000Z',
    ];

    $response = mock(Response::class);
    $response->shouldReceive('successful')->andReturn(true);
    $response->shouldReceive('json')->with('data')->andReturn($profileData);

    $this->api->shouldReceive('scanConnection')->once()->andReturn($response);

    Livewire::test(Scan::class)
        ->call('processQrCode', 'connectsnap://u/abc123xyz789abcdef012')
        ->assertSee('Connection added successfully!')
        ->assertSee('John Doe')
        ->assertSee('View Connections')
        ->assertSee('Scan Another');
});

test('shows error view with retry button', function () {
    Livewire::test(Scan::class)
        ->call('processQrCode', 'invalid')
        ->assertSee('Scan Failed')
        ->assertSee('Invalid QR code format')
        ->assertSee('Try Again');
});
