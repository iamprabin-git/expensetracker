<x-marketing-layout title="Membership expired" narrow>
    <div class="container px-3 w-100">
        <div class="site-auth-card text-center">
            <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center mb-4" style="width:4rem;height:4rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="32" height="32">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <h1 class="h4 fw-bold mb-2">Membership expired</h1>
            <p class="text-secondary mb-2">
                Your access ended@if($expiresAt) on {{ $expiresAt->format('M d, Y') }}@endif. Please contact support to renew your membership.
            </p>
            <p class="small text-secondary mb-4">
                <a href="{{ route('contact') }}">Contact us</a> for renewal.
            </p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn site-btn-outline">Log out</button>
            </form>
        </div>
    </div>
</x-marketing-layout>
