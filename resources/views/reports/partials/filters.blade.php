<div class="report-no-print report-page-wrap mb-4 mx-auto">
    <form method="GET" action="{{ route('reports.show', $reportKey) }}" class="card-panel grid grid-cols-12 gap-3 items-end">
        <div class="col-span-12 sm:col-span-6 col-span-12 md:col-span-4">
            <label class="label-app" for="from_date">From</label>
            <input type="date" name="from_date" id="from_date" value="{{ $filters['from_date'] }}" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs input-app">
        </div>
        <div class="col-span-12 sm:col-span-6 col-span-12 md:col-span-4">
            <label class="label-app" for="to_date">To / As of</label>
            <input type="date" name="to_date" id="to_date" value="{{ $filters['to_date'] }}" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs input-app">
        </div>
        <div class="col-span-12 md:col-span-4 flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary flex-1">Apply</button>
            <x-ui.button variant="outline" size="sm" href="{{ route('reports.show', $reportKey) }}">Clear</x-ui.button>
        </div>
    </form>
</div>
