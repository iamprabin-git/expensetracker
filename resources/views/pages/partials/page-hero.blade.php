@props(['title', 'lead' => null, 'badge' => null])

<section class="site-page-hero">
    <div class="container text-center">
        @if ($badge)
            <span class="badge rounded-pill text-bg-primary mb-3 px-3 py-2">{{ $badge }}</span>
        @endif
        <h1 class="site-page-hero__title">{{ $title }}</h1>
        @if ($lead)
            <p class="site-section-lead mx-auto mb-0" style="max-width: 42rem;">{{ $lead }}</p>
        @endif
    </div>
</section>
