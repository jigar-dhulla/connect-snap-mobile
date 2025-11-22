<div class="flex-1 flex flex-col justify-center">
    <flux:card class="space-y-6">
        <div class="text-center">
            <flux:heading size="xl">Welcome Back</flux:heading>
            <flux:text class="mt-2">Sign in to your account</flux:text>
        </div>

        <form wire:submit="login" class="space-y-4">
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
            />

            <div class="flex items-center justify-between">
                <flux:checkbox wire:model="remember" label="Remember me" />
            </div>

            <flux:button type="submit" variant="primary" class="w-full">
                <span wire:loading.remove wire:target="login">Sign In</span>
                <span wire:loading wire:target="login">Signing in...</span>
            </flux:button>
        </form>

        <div class="text-center">
            <flux:text size="sm">
                Don't have an account?
                <flux:link href="{{ route('register') }}" wire:navigate>Create one</flux:link>
            </flux:text>
        </div>
    </flux:card>
</div>