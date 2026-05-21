<div class="report-page report-page-wrap card-panel bg-white mx-auto mb-4">
    <div class="border-bottom pb-4 mb-4">
        <div class="flex flex-wrap justify-between align-items-start gap-3">
            <div>
                <p class="text-uppercase small font-semibold text-muted-foreground mb-1">Financial report</p>
                <h1 class="text-xl font-semibold tracking-tight font-bold mb-1">{{ $reportTitle }}</h1>
                <p class="mb-0 text-muted-foreground">{{ $user->name }} · {{ $user->email }}</p>
            </div>
            <div class="md:text-right">
                <p class="mb-1 small text-muted-foreground">Period</p>
                <p class="font-semibold mb-1">{{ $periodLabel }}</p>
                <p class="mb-0 small text-muted-foreground">Generated {{ $generatedAt->format('M d, Y g:i A') }}</p>
            </div>
        </div>
    </div>
</div>
