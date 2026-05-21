@props([
    'page',
    'contactItems',
    'company' => null,
])

@php
    $items = collect($contactItems)->filter(fn ($item) => filled($item['text'] ?? null));

    $iconPaths = [
        'Email' => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75',
        'Phone' => 'M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 6.75Z',
        'Hours' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'Address' => 'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z',
        'Response time' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    ];
    $defaultIcon = 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z';
@endphp

<section class="contact-section site-section" id="contact-form">
    <div class="mx-auto w-full max-w-6xl px-4">
        <div class="contact-section__layout">
            <aside class="contact-section__aside">
                <div class="contact-section__intro">
                    <h2 class="contact-section__title">{{ $page->extra('section_title', $page->extra('sidebar_title', 'Get in touch')) }}</h2>
                    <p class="contact-section__subtitle">
                        {{ $page->extra('section_subtitle', 'Reach our team for support, billing, or partnership questions. We read every message.') }}
                    </p>
                </div>

                @if ($items->isNotEmpty())
                    <ul class="contact-section__channels">
                        @foreach ($items as $item)
                            @php
                                $title = $item['title'] ?? 'Contact';
                                $text = $item['text'] ?? '';
                                $icon = $iconPaths[$title] ?? $defaultIcon;
                            @endphp
                            <li class="contact-section__channel">
                                <span class="contact-section__channel-icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                                    </svg>
                                </span>
                                <div class="contact-section__channel-body">
                                    <p class="contact-section__channel-label">{{ $title }}</p>
                                    <p class="contact-section__channel-value">
                                        @if ($title === 'Email')
                                            <a href="mailto:{{ $text }}">{{ $text }}</a>
                                        @elseif ($title === 'Phone')
                                            <a href="tel:{{ preg_replace('/\s+/', '', $text) }}">{{ $text }}</a>
                                        @else
                                            {!! nl2br(e($text)) !!}
                                        @endif
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="contact-section__aside-card">
                    <p class="contact-section__aside-card-title">Before you write</p>
                    <ul class="contact-section__tips">
                        <li>Check the <a href="{{ route('faq') }}" class="contact-section__link">FAQ</a> for quick answers</li>
                        <li>Include your account email if the request is account-related</li>
                        <li>We never ask for your password by email</li>
                    </ul>
                </div>
            </aside>

            <div class="contact-section__panel">
                <div class="contact-section__panel-header">
                    <h2 class="contact-section__form-title">{{ $page->extra('form_title', 'Send a message') }}</h2>
                    <p class="contact-section__form-subtitle">{{ $page->extra('form_subtitle', 'All fields are required. We typically reply within two business days.') }}</p>
                </div>

                <form method="POST" action="{{ route('contact.send') }}" class="contact-section__form" novalidate>
                    @csrf
                    <div class="contact-section__fields">
                        <div class="contact-section__field">
                            <x-ui.label for="contact-name">Full name</x-ui.label>
                            <x-ui.input
                                type="text"
                                name="name"
                                id="contact-name"
                                value="{{ old('name') }}"
                                placeholder="Your name"
                                required
                                autocomplete="name"
                                @class(['border-destructive ring-destructive/20' => $errors->has('name')])
                            />
                            <x-ui.field-error :messages="$errors->get('name')" />
                        </div>
                        <div class="contact-section__field">
                            <x-ui.label for="contact-email">Email address</x-ui.label>
                            <x-ui.input
                                type="email"
                                name="email"
                                id="contact-email"
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                required
                                autocomplete="email"
                                @class(['border-destructive ring-destructive/20' => $errors->has('email')])
                            />
                            <x-ui.field-error :messages="$errors->get('email')" />
                        </div>
                        <div class="contact-section__field contact-section__field--full">
                            <x-ui.label for="contact-subject">Subject</x-ui.label>
                            <x-ui.input
                                type="text"
                                name="subject"
                                id="contact-subject"
                                value="{{ old('subject') }}"
                                placeholder="How can we help?"
                                required
                                @class(['border-destructive ring-destructive/20' => $errors->has('subject')])
                            />
                            <x-ui.field-error :messages="$errors->get('subject')" />
                        </div>
                        <div class="contact-section__field contact-section__field--full">
                            <x-ui.label for="contact-message">Message</x-ui.label>
                            <x-ui.textarea
                                name="message"
                                id="contact-message"
                                rows="6"
                                placeholder="Tell us a bit more about your question…"
                                required
                                @class(['border-destructive ring-destructive/20' => $errors->has('message')])
                            >{{ old('message') }}</x-ui.textarea>
                            <x-ui.field-error :messages="$errors->get('message')" />
                        </div>
                    </div>
                    <div class="contact-section__actions">
                        <x-ui.button type="submit" class="contact-section__submit">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                            </svg>
                            Send message
                        </x-ui.button>
                        <p class="contact-section__privacy-note">
                            By submitting, you agree we may use your details only to respond to this inquiry.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
