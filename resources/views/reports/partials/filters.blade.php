<div class="report-no-print report-page-wrap mb-4 mx-auto">
    <form method="GET" action="{{ route('reports.show', $reportKey) }}" class="card-panel row g-3 align-items-end">
        <div class="col-12 col-sm-6 col-md-4">
            <label class="label-app" for="from_date">From</label>
            <input type="date" name="from_date" id="from_date" value="{{ $filters['from_date'] }}" class="form-control input-app">
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <label class="label-app" for="to_date">To / As of</label>
            <input type="date" name="to_date" id="to_date" value="{{ $filters['to_date'] }}" class="form-control input-app">
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Apply</button>
            <a href="{{ route('reports.show', $reportKey) }}" class="btn btn-sm btn-outline-secondary">Clear</a>
        </div>
    </form>
</div>
