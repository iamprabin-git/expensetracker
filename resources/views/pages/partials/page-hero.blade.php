@props(['title', 'lead' => null, 'badge' => null, 'image' => null])

<section class="site-page-hero">
    <div class="container text-center">
        @if ($badge)
            <span class="badge rounded-pill text-bg-primary mb-3 px-3 py-2">{{ $badge }}</span>
        @endif
        @if ($image)
            <img src="{{ $image }}" alt="" class="img-fluid rounded-4 shadow-sm mb-4 mx-auto d-block" style="max-height: 16rem; object-fit: cover;">
        @endif
        @if ($title)
            <h1 class="site-page-hero__title">{{ $title }}</h1>
        @endif
        @if ($lead)
            <p class="site-section-lead mx-auto mb-0" style="max-width: 42rem;">{{ $lead }}</p>
        @endif
    </div>
</section>
