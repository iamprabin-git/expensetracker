<div class="report-no-print report-page-wrap mx-auto mb-4">
    <form method="GET" action="{{ route('reports.show', $reportKey) }}" class="report-filters grid grid-cols-12 gap-3 items-end p-4 sm:p-5">
        <div class="col-span-12 sm:col-span-6 md:col-span-4">
            <label class="label-app" for="from_date">From</label>
            <input type="date" name="from_date" id="from_date" value="{{ $filters['from_date'] }}" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs">
        </div>
        <div class="col-span-12 sm:col-span-6 md:col-span-4">
            <label class="label-app" for="to_date">To / As of</label>
            <input type="date" name="to_date" id="to_date" value="{{ $filters['to_date'] }}" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs">
        </div>
        <div class="col-span-12 flex gap-2 md:col-span-4">
            <button type="submit" class="btn btn-sm btn-primary flex-1">Apply</button>
            <x-ui.button variant="outline" size="sm" href="{{ route('reports.show', $reportKey) }}">Clear</x-ui.button>
        </div>
    </form>
</div>
