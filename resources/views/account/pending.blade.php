<x-marketing-layout title="Account pending" narrow>
    <div class="mx-auto w-full max-w-6xl px-4 px-3 w-full">
        <div class="site-auth-card text-center">
            <div class="rounded-circle bg-warning-subtle text-warning inline-flex items-center justify-center mb-4" style="width:4rem;height:4rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="32" height="32">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h1 class="text-xl font-semibold tracking-tight font-bold mb-2">Account awaiting approval</h1>
            <p class="text-muted-foreground mb-4">
                Thanks for registering, {{ auth()->user()->name }}. An approval request was sent to the admin agent, and your login credentials were emailed to <strong>{{ auth()->user()->email }}</strong>.
                You can sign in anytime; the dashboard unlocks after an administrator approves your account and sets your membership.
                @if (auth()->user()->google_id)
                    You may also use <strong>Sign in with Google</strong> on the login page.
                @endif
            </p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ui.button type="submit" variant="outline">Log out</x-ui.button>
            </form>
        </div>
    </div>
</x-marketing-layout>
