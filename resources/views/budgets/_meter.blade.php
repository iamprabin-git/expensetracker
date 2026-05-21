@php
    $variant = $variant ?? 'primary';
    $status = $status ?? 'ok';
    $rawPercent = max((float) ($percent ?? 0), 0);
    $barPercent = min($rawPercent, 100);
@endphp
<div class="budget-meter budget-meter--{{ $variant }} budget-meter--{{ $status }}" role="progressbar" aria-valuenow="{{ round($barPercent) }}" aria-valuemin="0" aria-valuemax="100">
    <div class="budget-meter__bar" style="width: {{ $barPercent }}%"></div>
</div>
@if (!($hideLabel ?? false))
    <p class="text-sm text-muted-foreground mb-0 mt-1">{{ round($rawPercent, 1) }}% of budget</p>
@endif
