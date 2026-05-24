@php
    $isRegister = $mode === 'register';
@endphp

<div class="auth-hero__content">
    <a href="{{ url('/') }}" class="auth-hero__brand">
        <x-site-brand tag="span" class="auth-hero__brand-text" />
    </a>

    <div class="auth-hero__copy">
        <span class="auth-hero__badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Smart money management
        </span>
        <h2 class="auth-hero__title">
            {{ $isRegister ? 'Start your financial journey' : 'Your finances, beautifully organized' }}
        </h2>
        <p class="auth-hero__desc">
            Track income and expenses, plan monthly budgets, and generate professional reports — all in one secure dashboard.
        </p>
    </div>

    <figure class="auth-hero__visual">
        <div class="auth-hero__visual-frame">
            <img
                src="{{ asset('images/auth-finance-hero.svg') }}"
                alt=""
                width="560"
                height="420"
                loading="eager"
                decoding="async"
                class="auth-hero__image"
            >
        </div>
    </figure>

    <div class="auth-hero__stats" aria-hidden="true">
        <div class="auth-hero__stat">
            <span class="auth-hero__stat-value">12+</span>
            <span class="auth-hero__stat-label">Report types</span>
        </div>
        <div class="auth-hero__stat">
            <span class="auth-hero__stat-value">AI</span>
            <span class="auth-hero__stat-label">Receipt scan</span>
        </div>
        <div class="auth-hero__stat">
            <span class="auth-hero__stat-value">100%</span>
            <span class="auth-hero__stat-label">Your data</span>
        </div>
    </div>

    <ul class="auth-hero__features">
        <li>
            <span class="auth-hero__feature-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
            </span>
            P&amp;L, balance sheet &amp; trial balance
        </li>
        <li>
            <span class="auth-hero__feature-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
            </span>
            Budget planning by category
        </li>
        <li>
            <span class="auth-hero__feature-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>
            </span>
            Bank-grade secure access
        </li>
    </ul>
</div>
