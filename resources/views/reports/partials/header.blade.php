<div class="report-doc report-page-wrap mx-auto mb-4">
    <div class="report-doc__header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Financial report</p>
                <h1 class="mb-1 text-xl font-bold tracking-tight text-foreground">{{ $reportTitle }}</h1>
                <p class="mb-0 text-sm text-muted-foreground">{{ $user->name }} · {{ $user->email }}</p>
            </div>
            <div class="md:text-right">
                <p class="mb-1 text-xs text-muted-foreground">Period</p>
                <p class="mb-1 text-sm font-semibold text-foreground">{{ $periodLabel }}</p>
                <p class="mb-0 text-xs text-muted-foreground">Generated {{ $generatedAt->format('M d, Y g:i A') }}</p>
            </div>
        </div>
    </div>
</div>
