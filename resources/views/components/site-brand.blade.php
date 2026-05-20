@props(['tag' => 'span'])

@php
    $company = $company ?? null;
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => 'site-brand__text']) }}>
    @if ($company?->logoUrl())
        <img
            src="{{ $company->logoUrl() }}"
            alt="{{ $company->company_name }}"
            class="site-brand__logo"
            width="160"
            height="40"
        >
    @else
        {{ $company?->brand_name_primary ?? 'Mero' }}
        <span class="site-brand__accent">{{ $company?->brand_name_accent ?? 'Expense Tracker' }}</span>
    @endif
</{{ $tag }}>
