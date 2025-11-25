<div class="flex flex-col items-center justify-center min-h-[calc(100vh-140px)] px-4 py-6">
    {{-- Loading State --}}
    <div wire:loading.delay wire:target="refresh" class="absolute inset-0 bg-white/80 dark:bg-zinc-900/80 flex items-center justify-center z-10">
        <flux:icon name="arrow-path" class="w-8 h-8 animate-spin text-tech-blue" />
    </div>

    @if ($loading)
        {{-- Initial Loading State --}}
        <div class="flex flex-col items-center justify-center space-y-4">
            <flux:icon name="arrow-path" class="w-12 h-12 animate-spin text-tech-blue" />
            <flux:text class="text-zinc-500">Loading your QR code...</flux:text>
        </div>
    @else
        {{-- QR Code Display --}}
        <flux:card class="w-full max-w-sm p-6 space-y-6">
            {{-- User Info Header --}}
            <div class="text-center space-y-2">
                @if ($profile)
                    <flux:heading size="xl" class="font-bold leading-tight">
                        {{ $profile['user']['name'] ?? 'Your Name' }}
                    </flux:heading>
                    @if (!empty($profile['job_title']) || !empty($profile['company']))
                        <flux:text class="text-zinc-500 leading-snug">
                            {{ $profile['job_title'] ?? '' }}
                            @if (!empty($profile['job_title']) && !empty($profile['company']))
                                <span class="mx-1">at</span>
                            @endif
                            {{ $profile['company'] ?? '' }}
                        </flux:text>
                    @endif
                @endif
            </div>

            {{-- QR Code SVG --}}
            <div class="flex justify-center">
                @if ($qrCodeSvg)
                    <div data-qr-svg class="bg-white p-4 rounded-xl shadow-lg [&>svg]:w-56 [&>svg]:h-56">
                        {!! $qrCodeSvg !!}
                    </div>
                @else
                    <div class="w-56 h-56 bg-zinc-100 dark:bg-zinc-800 rounded-xl flex items-center justify-center">
                        <flux:icon name="qr-code" class="w-24 h-24 text-zinc-300 dark:text-zinc-600" />
                    </div>
                @endif
            </div>

            {{-- Instructions --}}
            <flux:text class="text-center text-sm text-zinc-400">
                Show this QR code to connect with others
            </flux:text>

            {{-- Action Buttons --}}
            <div class="space-y-3">
                <flux:button
                    href="{{ route('profile.edit') }}"
                    wire:navigate
                    variant="primary"
                    class="w-full"
                >
                    <flux:icon name="pencil-square" class="w-4 h-4 mr-2" />
                    Edit Profile
                </flux:button>

                @if ($qrCodeSvg)
                    <flux:button
                        x-data
                        x-on:click="
                            const svg = document.querySelector('[data-qr-svg]').innerHTML;
                            const blob = new Blob([svg], { type: 'image/svg+xml' });
                            const url = URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.href = url;
                            link.download = 'connectsnap-qr.svg';
                            link.click();
                            URL.revokeObjectURL(url);
                        "
                        variant="ghost"
                        class="w-full"
                    >
                        <flux:icon name="arrow-down-tray" class="w-4 h-4 mr-2" />
                        Download QR Code
                    </flux:button>
                @endif
            </div>
        </flux:card>

        {{-- Profile Completion Prompt --}}
        @if ($profile && (empty($profile['company']) || empty($profile['job_title']) || empty($profile['bio'])))
            <flux:callout variant="warning" class="mt-4 max-w-sm w-full">
                <flux:callout.heading>Complete your profile</flux:callout.heading>
                <flux:callout.text>
                    Add your company, job title, and bio to make better connections.
                </flux:callout.text>
            </flux:callout>
        @endif
    @endif
</div>
