<x-marketing-layout title="Account pending" narrow>
    <div class="container px-3 w-100">
        <div class="site-auth-card text-center">
            <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-4" style="width:4rem;height:4rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="32" height="32">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h1 class="h4 fw-bold mb-2">Account awaiting approval</h1>
            <p class="text-secondary mb-4">
                Thanks for registering, {{ auth()->user()->name }}. An approval request was sent to the admin agent, and your login credentials were emailed to <strong>{{ auth()->user()->email }}</strong>.
                You can sign in anytime; the dashboard unlocks after an administrator approves your account and sets your membership.
                @if (auth()->user()->google_id)
                    You may also use <strong>Sign in with Google</strong> on the login page.
                @endif
            </p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn site-btn-outline">Log out</button>
            </form>
        </div>
    </div>
</x-marketing-layout>
