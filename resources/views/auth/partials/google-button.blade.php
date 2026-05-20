@if (config('services.google.client_id') && config('services.google.client_secret'))
    <a
        href="{{ route('auth.google.redirect') }}"
        class="btn btn-outline-secondary w-100 d-inline-flex align-items-center justify-content-center gap-2 mb-4"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48" aria-hidden="true">
            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.654 32.657 29.083 36 24 36c-7.2 0-13-5.8-13-13s5.8-13 13-13c3.28 0 6.28 1.19 8.619 3.149l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.28 0 6.28 1.19 8.619 3.149l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.067 0-9.33-3.442-10.874-8.081l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
        </svg>
        Continue with Google
    </a>

    <div class="auth-divider d-flex align-items-center gap-3 mb-4">
        <hr class="flex-grow-1 m-0">
        <span class="small text-secondary">or</span>
        <hr class="flex-grow-1 m-0">
    </div>
@else
    <p class="small text-secondary mb-4">
        Google sign-in is not configured. Set <code>GOOGLE_CLIENT_ID</code> and <code>GOOGLE_CLIENT_SECRET</code> in your <code>.env</code> file.
    </p>
@endif
