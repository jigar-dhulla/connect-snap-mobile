<div class="flex-1 flex flex-col justify-center px-4">
    <flux:card class="space-y-8 p-6">
        <div class="text-center space-y-4">
            <img src="{{ asset('icon.png') }}" alt="ConnectSnap" class="w-24 h-24 mx-auto">
            <div class="space-y-2">
                <flux:heading size="2xl" class="font-bold">Welcome Back</flux:heading>
                <flux:text class="text-base">Sign in to your account</flux:text>
            </div>
        </div>

        <form wire:submit="login" class="space-y-5">
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
                placeholder="Enter your password"
                autocomplete="current-password"
                required
                viewable
            />

            <flux:button type="submit" variant="primary" class="w-full py-4 text-lg font-semibold">
                <span wire:loading.remove wire:target="login">Sign In</span>
                <span wire:loading wire:target="login">Signing in...</span>
            </flux:button>
        </form>

        <div class="text-center pt-4">
            <flux:text class="text-base">
                Don't have an account?
            </flux:text>
            <flux:link href="{{ route('register') }}" wire:navigate class="text-lg font-semibold mt-2 inline-block py-2">
                Create one
            </flux:link>
        </div>
    </flux:card>
</div>
