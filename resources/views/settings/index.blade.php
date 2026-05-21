@php
    $navItems = [
        [
            'id' => 'profile-photo',
            'label' => 'Profile photo',
            'danger' => false,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>',
        ],
        [
            'id' => 'profile-details',
            'label' => 'Profile details',
            'danger' => false,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>',
        ],
        [
            'id' => 'email',
            'label' => 'Email',
            'danger' => false,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>',
        ],
        [
            'id' => 'password',
            'label' => 'Password',
            'danger' => false,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>',
        ],
        [
            'id' => 'preferences',
            'label' => 'Currency & region',
            'danger' => false,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
        ],
        [
            'id' => 'delete-account',
            'label' => 'Delete account',
            'danger' => true,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>',
        ],
    ];
@endphp

<x-user-layout>
    <x-slot name="header">Settings</x-slot>
    <x-slot name="subheader">Manage your profile, security, and preferences.</x-slot>

    <div
        class="settings-page"
        x-data="{ active: 'profile-photo' }"
        x-init="
            const panels = $el.querySelectorAll('.settings-panel[id]');
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            active = entry.target.id;
                        }
                    });
                },
                { rootMargin: '-15% 0px -55% 0px', threshold: 0.1 }
            );
            panels.forEach((panel) => observer.observe(panel));
        "
    >
        <div class="settings-page__layout">
            <aside class="settings-page__sidebar">
                <div class="settings-page__profile-card">
                    <x-user-avatar :user="$user" size="lg" />
                    <div class="settings-page__profile-meta">
                        <p class="settings-page__profile-name">{{ $user->name }}</p>
                        <p class="settings-page__profile-email">{{ $user->email }}</p>
                    </div>
                </div>

                <nav class="settings-page__nav" aria-label="Settings sections">
                    <p class="settings-page__nav-label">Sections</p>
                    <ul class="settings-page__nav-list">
                        @foreach ($navItems as $item)
                            <li>
                                <a
                                    href="#{{ $item['id'] }}"
                                    class="settings-page__nav-link {{ $item['danger'] ? 'settings-page__nav-link--danger' : '' }}"
                                    :class="{ 'is-active': active === @js($item['id']) }"
                                    @click="active = @js($item['id'])"
                                >
                                    <span class="settings-page__nav-icon">{!! $item['icon'] !!}</span>
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </aside>

            <div class="settings-page__content">
                <x-settings.section
                    id="profile-photo"
                    title="Profile photo"
                    description="Upload a photo shown in the sidebar and header. JPG, PNG, or WebP up to 2 MB."
                >
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 9.186 4.5h5.628a2.31 2.31 0 0 1 2.359 1.675M6.827 6.175l-1.06 4.12M6.827 6.175H4.5m2.327 0 .884 3.432M15.75 17.25l-1.06-4.12m1.06 4.12.884-3.432m-.884 3.432h2.327M9.75 9.75h4.5" /></svg>
                    </x-slot:icon>

                    <div class="settings-avatar-block">
                        <x-user-avatar :user="$user" size="lg" />
                        <div class="settings-avatar-block__info">
                            <p class="settings-avatar-block__name">{{ $user->name }}</p>
                            <p class="settings-avatar-block__email">{{ $user->email }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('settings.avatar.update') }}" enctype="multipart/form-data" class="settings-form">
                        @csrf
                        @method('PUT')
                        <div class="settings-form__field settings-form__field--full">
                            <x-ui.label for="avatar">Choose image</x-ui.label>
                            <input
                                type="file"
                                name="avatar"
                                id="avatar"
                                accept="image/jpeg,image/png,image/webp"
                                class="settings-file-input @error('avatar') border-destructive @enderror"
                                required
                            >
                            <x-ui.field-error :messages="$errors->get('avatar')" />
                        </div>
                        <div class="settings-form__actions">
                            <x-ui.button type="submit">Upload photo</x-ui.button>
                        </div>
                    </form>

                    @if ($user->avatar_path)
                        <form method="POST" action="{{ route('settings.avatar.destroy') }}" class="settings-form__actions mt-2" onsubmit="return confirm('Remove your profile photo?');">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="destructive" size="sm">Remove photo</x-ui.button>
                        </form>
                    @endif
                </x-settings.section>

                <x-settings.section
                    id="profile-details"
                    title="Profile details"
                    description="Update your display name and phone number."
                >
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                    </x-slot:icon>

                    <form method="POST" action="{{ route('settings.profile.update') }}" class="settings-form settings-form--two-col">
                        @csrf
                        @method('PUT')
                        <div class="settings-form__field">
                            <x-ui.label for="name">Full name</x-ui.label>
                            <x-ui.input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $user->name) }}"
                                required
                                autocomplete="name"
                                @class(['border-destructive ring-destructive/20' => $errors->has('name')])
                            />
                            <x-ui.field-error :messages="$errors->get('name')" />
                        </div>
                        <div class="settings-form__field">
                            <x-ui.label for="phone">Phone number</x-ui.label>
                            <x-ui.input
                                type="tel"
                                name="phone"
                                id="phone"
                                value="{{ old('phone', $user->phone) }}"
                                placeholder="+1 555 000 0000"
                                autocomplete="tel"
                                @class(['border-destructive ring-destructive/20' => $errors->has('phone')])
                            />
                            <x-ui.field-error :messages="$errors->get('phone')" />
                        </div>
                        <div class="settings-form__actions settings-form__field--full">
                            <x-ui.button type="submit">Save profile</x-ui.button>
                        </div>
                    </form>
                </x-settings.section>

                <x-settings.section
                    id="email"
                    title="Email address"
                    description="Changing your email requires your current password. You may need to verify the new address."
                >
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                    </x-slot:icon>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <x-ui.alert variant="destructive" class="mb-4">
                            <p class="mb-0 text-sm">
                                Your email is not verified.
                                <form class="inline" method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button type="submit" class="font-semibold underline underline-offset-2">Resend verification email</button>
                                </form>
                            </p>
                        </x-ui.alert>
                    @endif

                    <form method="POST" action="{{ route('settings.email.update') }}" class="settings-form">
                        @csrf
                        @method('PUT')
                        <div class="settings-form__field">
                            <x-ui.label for="email">Email</x-ui.label>
                            <x-ui.input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                autocomplete="username"
                                @class(['border-destructive ring-destructive/20' => $errors->has('email')])
                            />
                            <x-ui.field-error :messages="$errors->get('email')" />
                        </div>
                        <div class="settings-form__field">
                            <x-ui.label for="email_current_password">Current password</x-ui.label>
                            <x-ui.input
                                type="password"
                                name="current_password"
                                id="email_current_password"
                                required
                                autocomplete="current-password"
                                @class(['border-destructive ring-destructive/20' => $errors->has('current_password')])
                            />
                            <x-ui.field-error :messages="$errors->get('current_password')" />
                        </div>
                        <div class="settings-form__actions">
                            <x-ui.button type="submit">Update email</x-ui.button>
                        </div>
                    </form>
                </x-settings.section>

                <x-settings.section
                    id="password"
                    title="Password"
                    description="Use a strong password with at least 8 characters."
                >
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                    </x-slot:icon>

                    <p class="settings-hint mb-4">
                        <a href="{{ route('password.request') }}">Forgot your password?</a> — we will email you a reset link.
                    </p>

                    <form method="POST" action="{{ route('settings.password.update') }}" class="settings-form settings-form--two-col">
                        @csrf
                        @method('PUT')
                        <div class="settings-form__field settings-form__field--full">
                            <x-ui.label for="current_password">Current password</x-ui.label>
                            <x-ui.input
                                type="password"
                                name="current_password"
                                id="current_password"
                                required
                                autocomplete="current-password"
                                @class(['border-destructive ring-destructive/20' => $errors->has('current_password')])
                            />
                            <x-ui.field-error :messages="$errors->get('current_password')" />
                        </div>
                        <div class="settings-form__field">
                            <x-ui.label for="password">New password</x-ui.label>
                            <x-ui.input
                                type="password"
                                name="password"
                                id="password"
                                required
                                autocomplete="new-password"
                                @class(['border-destructive ring-destructive/20' => $errors->has('password')])
                            />
                            <x-ui.field-error :messages="$errors->get('password')" />
                        </div>
                        <div class="settings-form__field">
                            <x-ui.label for="password_confirmation">Confirm new password</x-ui.label>
                            <x-ui.input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                autocomplete="new-password"
                            />
                        </div>
                        <div class="settings-form__actions settings-form__field--full">
                            <x-ui.button type="submit">Update password</x-ui.button>
                        </div>
                    </form>
                </x-settings.section>

                <x-settings.section
                    id="preferences"
                    title="Currency & region"
                    description="Amounts on your dashboard and transactions use your selected currency."
                >
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </x-slot:icon>

                    <form method="POST" action="{{ route('settings.preferences.update') }}" class="settings-form settings-form--two-col">
                        @csrf
                        @method('PUT')
                        <div class="settings-form__field">
                            <x-ui.label for="currency">Currency</x-ui.label>
                            <x-ui.select
                                name="currency"
                                id="currency"
                                required
                                @class(['border-destructive ring-destructive/20' => $errors->has('currency')])
                            >
                                @foreach ($currencies as $code => $meta)
                                    <option value="{{ $code }}" @selected(old('currency', $user->currency) === $code)>
                                        {{ $code }} — {{ $meta['name'] }} ({{ $meta['symbol'] }})
                                    </option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.field-error :messages="$errors->get('currency')" />
                        </div>
                        <div class="settings-form__field">
                            <x-ui.label for="timezone">Timezone</x-ui.label>
                            <x-ui.select
                                name="timezone"
                                id="timezone"
                                required
                                @class(['border-destructive ring-destructive/20' => $errors->has('timezone')])
                            >
                                @foreach ($timezones as $tz)
                                    <option value="{{ $tz }}" @selected(old('timezone', $user->timezone) === $tz)>{{ $tz }}</option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.field-error :messages="$errors->get('timezone')" />
                        </div>
                        <div class="settings-form__field settings-form__field--full">
                            <x-ui.label for="locale">Language</x-ui.label>
                            <x-ui.select
                                name="locale"
                                id="locale"
                                required
                                @class(['border-destructive ring-destructive/20' => $errors->has('locale')])
                            >
                                @foreach ($locales as $code => $label)
                                    <option value="{{ $code }}" @selected(old('locale', $user->locale) === $code)>{{ $label }}</option>
                                @endforeach
                            </x-ui.select>
                            <x-ui.field-error :messages="$errors->get('locale')" />
                        </div>
                        <div class="settings-form__field settings-form__field--full">
                            <div class="settings-checkbox-row">
                                <input type="hidden" name="notification_sound_enabled" value="0">
                                <x-ui.checkbox
                                    name="notification_sound_enabled"
                                    id="notification_sound_enabled"
                                    value="1"
                                    @checked(old('notification_sound_enabled', $user->notification_sound_enabled ?? true))
                                />
                                <label for="notification_sound_enabled">
                                    Play bell sound when a new in-app notification arrives
                                </label>
                            </div>
                        </div>
                        <div class="settings-form__field settings-form__field--full">
                            <p class="settings-preview">
                                Preview: <x-money :amount="1234.56" :user="$user" class="font-semibold" />
                            </p>
                        </div>
                        <div class="settings-form__actions settings-form__field--full">
                            <x-ui.button type="submit">Save preferences</x-ui.button>
                        </div>
                    </form>
                </x-settings.section>

                <x-settings.section
                    id="delete-account"
                    title="Delete account"
                    description="Permanently delete your account and all associated data. This cannot be undone."
                    danger
                >
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                    </x-slot:icon>

                    <form
                        method="POST"
                        action="{{ route('settings.account.destroy') }}"
                        class="settings-form settings-form--two-col"
                        onsubmit="return confirm('Are you sure you want to delete your account?');"
                    >
                        @csrf
                        @method('DELETE')
                        <div class="settings-form__field settings-form__field--full">
                            <x-ui.label for="delete_password">Confirm with password</x-ui.label>
                            <x-ui.input
                                type="password"
                                name="password"
                                id="delete_password"
                                required
                                autocomplete="current-password"
                                @class(['border-destructive ring-destructive/20' => $errors->has('password', 'accountDeletion')])
                            />
                            <x-ui.field-error :messages="$errors->get('password', 'accountDeletion')" />
                        </div>
                        <div class="settings-form__actions settings-form__field--full">
                            <x-ui.button type="submit" variant="destructive">Delete my account</x-ui.button>
                        </div>
                    </form>
                </x-settings.section>
            </div>
        </div>
    </div>
</x-user-layout>
