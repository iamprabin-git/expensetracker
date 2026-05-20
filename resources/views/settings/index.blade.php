<x-user-layout>
    <x-slot name="header">Settings</x-slot>
    <x-slot name="subheader">Manage your profile, security, and preferences.</x-slot>

    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <nav class="card-panel settings-nav sticky-lg-top" style="top: 5.5rem;" aria-label="Settings sections">
                <p class="small text-secondary text-uppercase fw-semibold mb-3">Sections</p>
                <ul class="list-unstyled settings-nav__list mb-0">
                    <li><a href="#profile-photo" class="settings-nav__link">Profile photo</a></li>
                    <li><a href="#profile-details" class="settings-nav__link">Profile details</a></li>
                    <li><a href="#email" class="settings-nav__link">Email</a></li>
                    <li><a href="#password" class="settings-nav__link">Password</a></li>
                    <li><a href="#preferences" class="settings-nav__link">Currency & region</a></li>
                    <li><a href="#delete-account" class="settings-nav__link text-danger">Delete account</a></li>
                </ul>
            </nav>
        </div>

        <div class="col-12 col-xl-8 d-grid gap-4">
            {{-- Profile photo --}}
            <section id="profile-photo" class="card-panel">
                <h2 class="h5 fw-semibold mb-1">Profile photo</h2>
                <p class="small text-secondary mb-4">Upload a photo shown in the sidebar and header. JPG, PNG, or WebP up to 2 MB.</p>

                <div class="d-flex flex-wrap align-items-center gap-4 mb-4">
                    <x-user-avatar :user="$user" size="lg" />
                    <div>
                        <p class="fw-semibold mb-0">{{ $user->name }}</p>
                        <p class="small text-secondary mb-0">{{ $user->email }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('settings.avatar.update') }}" enctype="multipart/form-data" class="row g-3 align-items-end">
                    @csrf
                    @method('PUT')
                    <div class="col-md-8">
                        <label class="label-app" for="avatar">Choose image</label>
                        <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/webp" class="form-control input-app @error('avatar') is-invalid @enderror" required>
                        @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary site-btn-primary w-100">Upload photo</button>
                    </div>
                </form>

                @if ($user->avatar_path)
                    <form method="POST" action="{{ route('settings.avatar.destroy') }}" class="mt-3" onsubmit="return confirm('Remove your profile photo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove photo</button>
                    </form>
                @endif
            </section>

            {{-- Profile details --}}
            <section id="profile-details" class="card-panel">
                <h2 class="h5 fw-semibold mb-1">Profile details</h2>
                <p class="small text-secondary mb-4">Update your display name and phone number.</p>

                <form method="POST" action="{{ route('settings.profile.update') }}" class="row g-3">
                    @csrf
                    @method('PUT')
                    <div class="col-md-6">
                        <label class="label-app" for="name">Full name</label>
                        <input type="text" name="name" id="name" class="input-app form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="label-app" for="phone">Phone number</label>
                        <input type="tel" name="phone" id="phone" class="input-app form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="+1 555 000 0000">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary site-btn-primary">Save profile</button>
                    </div>
                </form>
            </section>

            {{-- Email --}}
            <section id="email" class="card-panel">
                <h2 class="h5 fw-semibold mb-1">Email address</h2>
                <p class="small text-secondary mb-4">Changing your email requires your current password. You may need to verify the new address.</p>

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="alert alert-warning small">
                        Your email is not verified.
                        <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm p-0 align-baseline">Resend verification email</button>
                        </form>
                    </div>
                @endif

                <form method="POST" action="{{ route('settings.email.update') }}" class="row g-3">
                    @csrf
                    @method('PUT')
                    <div class="col-12">
                        <label class="label-app" for="email">Email</label>
                        <input type="email" name="email" id="email" class="input-app form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="label-app" for="email_current_password">Current password</label>
                        <input type="password" name="current_password" id="email_current_password" class="input-app form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary site-btn-primary">Update email</button>
                    </div>
                </form>
            </section>

            {{-- Password --}}
            <section id="password" class="card-panel">
                <h2 class="h5 fw-semibold mb-1">Password</h2>
                <p class="small text-secondary mb-4">
                    Use a strong password with at least 8 characters.
                    <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot your password?</a>
                    — we will email you a reset link.
                </p>

                <form method="POST" action="{{ route('settings.password.update') }}" class="row g-3">
                    @csrf
                    @method('PUT')
                    <div class="col-12">
                        <label class="label-app" for="current_password">Current password</label>
                        <input type="password" name="current_password" id="current_password" class="input-app form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="label-app" for="password">New password</label>
                        <input type="password" name="password" id="password" class="input-app form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="label-app" for="password_confirmation">Confirm new password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="input-app form-control" required autocomplete="new-password">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary site-btn-primary">Update password</button>
                    </div>
                </form>
            </section>

            {{-- Preferences --}}
            <section id="preferences" class="card-panel">
                <h2 class="h5 fw-semibold mb-1">Currency & region</h2>
                <p class="small text-secondary mb-4">Amounts on your dashboard and transactions use your selected currency.</p>

                <form method="POST" action="{{ route('settings.preferences.update') }}" class="row g-3">
                    @csrf
                    @method('PUT')
                    <div class="col-md-4">
                        <label class="label-app" for="currency">Currency</label>
                        <select name="currency" id="currency" class="input-app form-select @error('currency') is-invalid @enderror" required>
                            @foreach ($currencies as $code => $meta)
                                <option value="{{ $code }}" @selected(old('currency', $user->currency) === $code)>
                                    {{ $code }} — {{ $meta['name'] }} ({{ $meta['symbol'] }})
                                </option>
                            @endforeach
                        </select>
                        @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="label-app" for="timezone">Timezone</label>
                        <select name="timezone" id="timezone" class="input-app form-select @error('timezone') is-invalid @enderror" required>
                            @foreach ($timezones as $tz)
                                <option value="{{ $tz }}" @selected(old('timezone', $user->timezone) === $tz)>{{ $tz }}</option>
                            @endforeach
                        </select>
                        @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="label-app" for="locale">Language</label>
                        <select name="locale" id="locale" class="input-app form-select @error('locale') is-invalid @enderror" required>
                            @foreach ($locales as $code => $label)
                                <option value="{{ $code }}" @selected(old('locale', $user->locale) === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check mb-3">
                            <input type="hidden" name="notification_sound_enabled" value="0">
                            <input
                                type="checkbox"
                                name="notification_sound_enabled"
                                id="notification_sound_enabled"
                                value="1"
                                class="form-check-input"
                                @checked(old('notification_sound_enabled', $user->notification_sound_enabled ?? true))
                            >
                            <label class="form-check-label" for="notification_sound_enabled">
                                Play bell sound when a new in-app notification arrives
                            </label>
                        </div>
                        <p class="small text-secondary mb-2">Preview: <x-money :amount="1234.56" :user="$user" class="fw-semibold" /></p>
                        <button type="submit" class="btn btn-primary site-btn-primary">Save preferences</button>
                    </div>
                </form>
            </section>

            {{-- Delete account --}}
            <section id="delete-account" class="card-panel border border-danger-subtle">
                <h2 class="h5 fw-semibold text-danger mb-1">Delete account</h2>
                <p class="small text-secondary mb-4">Permanently delete your account and all associated data. This cannot be undone.</p>

                <form method="POST" action="{{ route('settings.account.destroy') }}" class="row g-3" onsubmit="return confirm('Are you sure you want to delete your account?');">
                    @csrf
                    @method('DELETE')
                    <div class="col-md-6">
                        <label class="label-app" for="delete_password">Confirm with password</label>
                        <input type="password" name="password" id="delete_password" class="input-app form-control @error('password', 'accountDeletion') is-invalid @enderror" required>
                        @error('password', 'accountDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-outline-danger">Delete my account</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-user-layout>
