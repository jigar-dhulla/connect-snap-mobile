<div class="flex flex-col min-h-[calc(100vh-140px)] px-4 py-6">
    {{-- Processing Overlay --}}
    @if ($processing)
        <div class="absolute inset-0 bg-white/90 dark:bg-zinc-900/90 flex items-center justify-center z-20">
            <div class="text-center space-y-4">
                <flux:icon name="arrow-path" class="w-12 h-12 animate-spin text-tech-blue mx-auto" />
                <flux:text class="text-zinc-600 dark:text-zinc-400">Processing QR code...</flux:text>
            </div>
        </div>
    @endif

    {{-- Initial State - Scan Prompt --}}
    @if (!$scannedProfile && !$error)
        <div class="flex-1 flex flex-col items-center justify-center space-y-6">
            <flux:card class="w-full max-w-sm p-6 text-center space-y-6">
                <div class="space-y-4">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-tech-blue/10">
                        <flux:icon name="qr-code" class="w-10 h-10 text-tech-blue" />
                    </div>
                    <flux:heading size="xl">Scan QR Code</flux:heading>
                    <flux:text class="text-zinc-500">
                        Point your camera at a ConnectSnap QR code to add a new connection.
                    </flux:text>
                </div>

                <flux:button wire:click="openScanner" variant="primary" class="w-full">
                    <flux:icon name="camera" class="w-5 h-5 mr-2" />
                    Open Scanner
                </flux:button>
            </flux:card>
        </div>
    @endif

    {{-- Error State --}}
    @if ($error && !$scannedProfile)
        <div class="flex-1 flex flex-col items-center justify-center space-y-6">
            <flux:card class="w-full max-w-sm p-6 text-center space-y-6">
                <div class="space-y-4">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-100 dark:bg-red-900/30">
                        <flux:icon name="exclamation-circle" class="w-10 h-10 text-red-500" />
                    </div>
                    <flux:heading size="lg">Scan Failed</flux:heading>
                    <flux:text class="text-zinc-500">{{ $error }}</flux:text>
                </div>

                <div class="space-y-3">
                    <flux:button wire:click="openScanner" variant="primary" class="w-full">
                        <flux:icon name="arrow-path" class="w-4 h-4 mr-2" />
                        Try Again
                    </flux:button>
                    <flux:button wire:click="clearResult" variant="ghost" class="w-full">
                        Cancel
                    </flux:button>
                </div>
            </flux:card>
        </div>
    @endif

    {{-- Success State - Show Scanned Profile --}}
    @if ($scannedProfile)
        <div class="flex-1 flex flex-col space-y-4">
            {{-- Success Badge --}}
            @if ($success)
                <div class="text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-100 dark:bg-green-900/30">
                        <flux:icon name="check-circle" class="w-5 h-5 text-green-500" />
                        <flux:text class="text-green-600 dark:text-green-400 font-medium">{{ $success }}</flux:text>
                    </div>
                </div>
            @endif

            {{-- Scanned Profile Card --}}
            <flux:card class="p-5 space-y-5">
                {{-- Profile Header --}}
                <div class="flex items-center gap-4">
                    @if (!empty($scannedProfile['connected_user']['profile_photo_url']))
                        <img
                            src="{{ $scannedProfile['connected_user']['profile_photo_url'] }}"
                            alt="{{ $scannedProfile['connected_user']['name'] }}"
                            class="w-20 h-20 rounded-full object-cover"
                        />
                    @else
                        <div class="w-20 h-20 rounded-full bg-tech-blue/10 flex items-center justify-center">
                            <flux:icon name="user" class="w-10 h-10 text-tech-blue" />
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <flux:heading size="xl" class="truncate leading-tight">
                            {{ $scannedProfile['connected_user']['name'] ?? 'Unknown' }}
                        </flux:heading>
                        @if (!empty($scannedProfile['connected_profile']['job_title']) || !empty($scannedProfile['connected_profile']['company']))
                            <flux:text class="text-zinc-500 text-sm leading-snug">
                                {{ $scannedProfile['connected_profile']['job_title'] ?? '' }}
                                @if (!empty($scannedProfile['connected_profile']['job_title']) && !empty($scannedProfile['connected_profile']['company']))
                                    at
                                @endif
                                {{ $scannedProfile['connected_profile']['company'] ?? '' }}
                            </flux:text>
                        @endif
                    </div>
                </div>

                {{-- Bio --}}
                @if (!empty($scannedProfile['connected_profile']['bio']))
                    <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:text class="text-zinc-600 dark:text-zinc-400">
                            {{ $scannedProfile['connected_profile']['bio'] }}
                        </flux:text>
                    </div>
                @endif

                {{-- Contact Info --}}
                @if (!empty($scannedProfile['connected_user']['email']) || !empty($scannedProfile['connected_profile']['phone']) || !empty($scannedProfile['connected_profile']['social_url']))
                    <div class="space-y-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        @if (!empty($scannedProfile['connected_user']['email']))
                            <div class="flex items-center gap-3">
                                <flux:icon name="envelope" class="w-5 h-5 text-zinc-400" />
                                <flux:text class="text-zinc-600 dark:text-zinc-400">
                                    {{ $scannedProfile['connected_user']['email'] }}
                                </flux:text>
                            </div>
                        @endif
                        @if (!empty($scannedProfile['connected_profile']['phone']))
                            <div class="flex items-center gap-3">
                                <flux:icon name="phone" class="w-5 h-5 text-zinc-400" />
                                <flux:text class="text-zinc-600 dark:text-zinc-400">
                                    {{ $scannedProfile['connected_profile']['phone'] }}
                                </flux:text>
                            </div>
                        @endif
                        @if (!empty($scannedProfile['connected_profile']['social_url']))
                            <div class="flex items-center gap-3">
                                <flux:icon name="link" class="w-5 h-5 text-zinc-400" />
                                <a href="{{ $scannedProfile['connected_profile']['social_url'] }}" target="_blank" class="text-tech-blue hover:underline truncate">
                                    {{ $scannedProfile['connected_profile']['social_url'] }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </flux:card>

            {{-- Action Buttons --}}
            <div class="space-y-3 mt-auto pt-4">
                <flux:button
                    href="{{ route('connections.index') }}"
                    wire:navigate
                    variant="primary"
                    class="w-full"
                >
                    <flux:icon name="users" class="w-4 h-4 mr-2" />
                    View Connections
                </flux:button>

                <flux:button wire:click="scanAgain" variant="ghost" class="w-full">
                    <flux:icon name="qr-code" class="w-4 h-4 mr-2" />
                    Scan Another
                </flux:button>
            </div>
        </div>
    @endif
</div>
