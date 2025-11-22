<div class="flex-1 flex flex-col justify-center">
    <flux:card class="space-y-6">
        <div class="text-center">
            <flux:heading size="xl">Create Account</flux:heading>
            <flux:text class="mt-2">Join ConnectSnap today</flux:text>
        </div>

        <form wire:submit="register" class="space-y-4">
            <flux:input
                wire:model="name"
                label="Full Name"
                type="text"
                placeholder="John Doe"
                autocomplete="name"
                required
            />

            <flux:input
                wire:model="email"
                label="Email"
                type="email"
                placeholder="you@example.com"
                autocomplete="email"
                required
            />

            <flux:input
                wire:model="password"
                label="Password"
                type="password"
                placeholder="Min. 8 characters"
                autocomplete="new-password"
                required
            />

            <flux:input
                wire:model="password_confirmation"
                label="Confirm Password"
                type="password"
                placeholder="Confirm your password"
                autocomplete="new-password"
                required
            />

            <flux:button type="submit" variant="primary" class="w-full">
                <span wire:loading.remove wire:target="register">Create Account</span>
                <span wire:loading wire:target="register">Creating account...</span>
            </flux:button>
        </form>

        <div class="text-center">
            <flux:text size="sm">
                Already have an account?
                <flux:link href="{{ route('login') }}" wire:navigate>Sign in</flux:link>
            </flux:text>
        </div>
    </flux:card>
</div>