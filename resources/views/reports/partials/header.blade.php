<div class="report-page card-panel bg-white mx-auto mb-4" style="max-width: 56rem;">
    <div class="border-bottom pb-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <p class="text-uppercase small fw-semibold text-secondary mb-1">Financial report</p>
                <h1 class="h4 fw-bold mb-1">{{ $reportTitle }}</h1>
                <p class="mb-0 text-secondary">{{ $user->name }} · {{ $user->email }}</p>
            </div>
            <div class="text-md-end">
                <p class="mb-1 small text-secondary">Period</p>
                <p class="fw-semibold mb-1">{{ $periodLabel }}</p>
                <p class="mb-0 small text-secondary">Generated {{ $generatedAt->format('M d, Y g:i A') }}</p>
            </div>
        </div>
    </div>
</div>
