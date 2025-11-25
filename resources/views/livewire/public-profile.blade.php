<div class="flex-1 flex flex-col items-center justify-center px-4 py-6">
    {{-- Loading State --}}
    @if ($loading)
        <div class="text-center space-y-4">
            <flux:icon name="arrow-path" class="w-12 h-12 animate-spin text-tech-blue mx-auto" />
            <flux:text class="text-zinc-500">Loading profile...</flux:text>
        </div>
    @elseif ($profile)
        {{-- Profile Display --}}
        <div class="w-full max-w-sm space-y-4">
            {{-- Profile Card --}}
            <flux:card class="p-6 space-y-5">
                {{-- Profile Header --}}
                <div class="text-center space-y-4">
                    @if (!empty($profile['profile_photo_url']))
                        <img
                            src="{{ $profile['profile_photo_url'] }}"
                            alt="{{ $profile['name'] ?? 'Profile' }}"
                            class="w-24 h-24 rounded-full object-cover mx-auto"
                        />
                    @else
                        <div class="w-24 h-24 rounded-full bg-tech-blue/10 flex items-center justify-center mx-auto">
                            <flux:icon name="user" class="w-12 h-12 text-tech-blue" />
                        </div>
                    @endif

                    <div class="space-y-1">
                        <flux:heading size="xl" class="leading-tight">
                            {{ $profile['name'] ?? 'Unknown' }}
                        </flux:heading>
                        @if (!empty($profile['job_title']) || !empty($profile['company']))
                            <flux:text class="text-zinc-500 leading-snug">
                                {{ $profile['job_title'] ?? '' }}
                                @if (!empty($profile['job_title']) && !empty($profile['company']))
                                    at
                                @endif
                                {{ $profile['company'] ?? '' }}
                            </flux:text>
                        @endif
                    </div>
                </div>

                {{-- Bio --}}
                @if (!empty($profile['bio']))
                    <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <flux:text class="text-zinc-600 dark:text-zinc-400 text-center">
                            {{ $profile['bio'] }}
                        </flux:text>
                    </div>
                @endif

                {{-- Contact Info --}}
                @if (!empty($profile['social_url']))
                    <div class="space-y-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center gap-3">
                            <flux:icon name="link" class="w-5 h-5 text-zinc-400" />
                            <a href="{{ $profile['social_url'] }}" target="_blank" class="text-tech-blue hover:underline truncate">
                                {{ $profile['social_url'] }}
                            </a>
                        </div>
                    </div>
                @endif
            </flux:card>

            {{-- Action Buttons --}}
            <div class="space-y-3">
                <flux:button
                    wire:click="saveConnection"
                    wire:loading.attr="disabled"
                    variant="primary"
                    class="w-full"
                >
                    <span wire:loading.remove wire:target="saveConnection" class="inline-flex items-center justify-center">
                        <flux:icon name="user-plus" class="w-4 h-4 mr-2" />
                        {{ $isLoggedIn ? 'Save Connection' : 'Login to Connect' }}
                    </span>
                    <span wire:loading wire:target="saveConnection">
                        Saving...
                    </span>
                </flux:button>

                @if ($isLoggedIn)
                    <flux:button href="{{ route('home') }}" wire:navigate variant="ghost" class="w-full">
                        <flux:icon name="home" class="w-4 h-4 mr-2" />
                        Go Home
                    </flux:button>
                @else
                    <flux:button href="{{ route('register') }}" wire:navigate variant="ghost" class="w-full">
                        <flux:icon name="user-plus" class="w-4 h-4 mr-2" />
                        Create Account
                    </flux:button>
                @endif
            </div>
        </div>
    @endif
</div>