<div class="flex-1 flex flex-col justify-center px-4">
    <flux:card class="space-y-8 p-6">
        <div class="text-center space-y-4">
            <img src="{{ asset('icon.png') }}" alt="ConnectSnap" class="w-24 h-24 mx-auto">
            <div class="space-y-2">
                <flux:heading size="2xl" class="font-bold">Create Account</flux:heading>
                <flux:text class="text-base">Join ConnectSnap today</flux:text>
            </div>
        </div>

        <form wire:submit="register" class="space-y-5">
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
                viewable
            />

            <flux:input
                wire:model="password_confirmation"
                label="Confirm Password"
                type="password"
                placeholder="Confirm your password"
                autocomplete="new-password"
                required
                viewable
            />

            <flux:button type="submit" variant="primary" class="w-full py-4 text-lg font-semibold">
                <span wire:loading.remove wire:target="register">Create Account</span>
                <span wire:loading wire:target="register">Creating account...</span>
            </flux:button>
        </form>

        <div class="text-center pt-4">
            <flux:text class="text-base">
                Already have an account?
            </flux:text>
            <flux:link href="{{ route('login') }}" wire:navigate class="text-lg font-semibold mt-2 inline-block py-2">
                Sign in
            </flux:link>
        </div>
    </flux:card>
</div>
