<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\AdminUser;


new class extends Component
{
public string $email = '';
    public string $password = '';
    public string $error = '';

    public function login(): void
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = AdminUser::where('email', $this->email)->first();

        if ($user && password_verify($this->password, $user->password)) {
            Auth::guard('admin')->loginUsingId($user->id);
            $this->redirect(route('admin.dashboard'), navigate: true);
        } else {
            $this->error = 'Invalid credentials.';
        }
    }

    public function fillDemo(string $type): void
    {
        if ($type === 'admin') {
            $this->email = 'admin@lumina.health';
            $this->password = 'admin123';
        } else {
            $this->email = 'editor@lumina.health';
            $this->password = 'editor123';
        }
    }

    public function render()
    {
        return $this->view([
            'isLocal' => app()->environment('local'),
        ])->layout('layouts.clean', ['title' => 'Admin Login']);
    }
};

?>
<div class="min-h-dvh grid lg:grid-cols-2 bg-background">
    <div class="hidden lg:flex flex-col justify-between bg-primary text-primary-foreground p-12 relative overflow-hidden">
        <div class="flex items-center gap-2.5">
            <div class="size-10 rounded-lg bg-white/15 grid place-items-center font-bold text-lg">S</div>
            <div class="font-bold text-lg">Shubham International</div>
        </div>
        <div class="relative z-10 max-w-md">
            @svg('lucide-shield-check', 'h-10 w-10 mb-5 text-secondary')
            <h2 class="text-4xl font-bold tracking-tight leading-tight">Control every pixel of the patient experience.</h2>
            <p class="mt-4 text-primary-foreground/70 leading-relaxed">Manage doctors, departments, appointments, content, and site-wide settings from one production-ready admin panel.</p>
        </div>
        <p class="text-xs text-primary-foreground/50">&copy; Shubham International Hospital</p>
        <div class="absolute -bottom-32 -right-32 size-80 rounded-full bg-secondary/20 blur-3xl" />
    </div>

    <div class="flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 lg:hidden flex items-center gap-2.5">
                <div class="size-9 rounded-lg bg-primary text-primary-foreground grid place-items-center font-bold">S</div>
                <div class="font-bold">Admin</div>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Sign in to admin</h1>
            <p class="text-sm text-muted-foreground mt-1.5">Use your administrator credentials.</p>

            @if($error)
                <p class="mt-4 text-sm text-red-500">{{ $error }}</p>
            @endif

            <form wire:submit="login" class="mt-8 space-y-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5">Email</label>
                    <input type="email" wire:model="email" autocomplete="email" required class="w-full px-3 py-2.5 text-sm rounded-md bg-background border border-border focus:outline-none focus:ring-2 focus:ring-primary/30" />
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5">Password</label>
                    <input type="password" wire:model="password" autocomplete="current-password" required class="w-full px-3 py-2.5 text-sm rounded-md bg-background border border-border focus:outline-none focus:ring-2 focus:ring-primary/30" />
                </div>
                <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-primary py-2.5 text-sm font-semibold text-primary-foreground shadow-card hover:opacity-90 transition-opacity disabled:opacity-60">
                    @svg('lucide-log-in', 'h-4 w-4')
                    <span wire:loading.remove>Sign in</span>
                    <span wire:loading>Signing in&hellip;</span>
                </button>
            </form>

            @if($isLocal && !$email && !$password)
                <div class="mt-8 p-4 rounded-md bg-muted/50 border border-border">
                    <p class="text-xs font-semibold mb-2">Demo credentials (dev only)</p>
                    <div class="text-xs text-muted-foreground space-y-1 font-mono">
                        <button type="button" wire:click="fillDemo('admin')" class="block hover:text-foreground transition-colors">admin@lumina.health &middot; admin123</button>
                        <button type="button" wire:click="fillDemo('editor')" class="block hover:text-foreground transition-colors">editor@lumina.health &middot; editor123</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
