@php
    $user = auth()->user();
    $monthKey = $month->format('Y-m');
    $prevMonth = $month->copy()->subMonth()->format('Y-m');
    $nextMonth = $month->copy()->addMonth()->format('Y-m');
@endphp

<x-user-layout>
    <x-slot name="header">Budget planning</x-slot>
    <x-slot name="subheader">Set monthly goals, allocate category budgets, and track actual spending from your transactions.</x-slot>
    <x-slot name="headerActions">
        @if ($can_copy_previous ?? false)
            <form method="POST" action="{{ route('budgets.duplicate') }}" class="d-inline">
                @csrf
                <input type="hidden" name="month" value="{{ $monthKey }}">
                <button type="submit" class="btn btn-outline-secondary">Copy from last month</button>
            </form>
        @endif
    </x-slot>

    @if (session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <div class="card-panel mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('budgets.index', ['month' => $prevMonth]) }}" class="btn btn-sm btn-outline-secondary" aria-label="Previous month">&larr;</a>
                <div>
                    <h2 class="h5 fw-semibold mb-0">{{ $month->format('F Y') }}</h2>
                    <p class="small text-secondary mb-0">{{ $plan->name ?? ($month->format('F Y').' Budget') }}</p>
                </div>
                <a href="{{ route('budgets.index', ['month' => $nextMonth]) }}" class="btn btn-sm btn-outline-secondary" aria-label="Next month">&rarr;</a>
            </div>
            <form method="GET" action="{{ route('budgets.index') }}" class="d-flex align-items-center gap-2">
                <label for="month" class="small text-secondary mb-0">Jump to</label>
                <input type="month" id="month" name="month" class="form-control form-control-sm" value="{{ $monthKey }}" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card-panel h-100">
                <p class="small text-secondary mb-1">Income goal</p>
                <p class="h4 fw-bold text-success mb-2">{{ $user->formatMoney($summary['income_goal']) }}</p>
                <p class="small mb-2">Actual: <strong>{{ $user->formatMoney($summary['income_actual']) }}</strong></p>
                @include('budgets._meter', [
                    'percent' => $summary['income_percent'],
                    'status' => $summary['income_actual'] >= $summary['income_goal'] && $summary['income_goal'] > 0 ? 'ok' : ($summary['income_percent'] >= 80 ? 'warning' : 'ok'),
                    'variant' => 'success',
                ])
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card-panel h-100">
                <p class="small text-secondary mb-1">Expense limit</p>
                <p class="h4 fw-bold text-danger mb-2">{{ $user->formatMoney($summary['expense_limit']) }}</p>
                <p class="small mb-2">Spent: <strong>{{ $user->formatMoney($summary['expense_actual']) }}</strong></p>
                @include('budgets._meter', [
                    'percent' => $summary['expense_percent'],
                    'status' => $summary['expense_actual'] > $summary['expense_limit'] && $summary['expense_limit'] > 0 ? 'over' : ($summary['expense_percent'] >= 80 ? 'warning' : 'ok'),
                    'variant' => 'danger',
                ])
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card-panel h-100">
                <p class="small text-secondary mb-1">Net (actual)</p>
                <p class="h4 fw-bold mb-2 {{ $summary['net_actual'] >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $user->formatMoney($summary['net_actual']) }}
                </p>
                <p class="small text-secondary mb-0">Planned net: {{ $user->formatMoney($summary['net_planned']) }}</p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card-panel h-100">
                <p class="small text-secondary mb-1">Remaining budget</p>
                <p class="h4 fw-bold mb-2">{{ $user->formatMoney($summary['expense_remaining']) }}</p>
                <p class="small text-secondary mb-0">
                    Category budgets: {{ $user->formatMoney($summary['expense_budgeted_categories']) }} expense /
                    {{ $user->formatMoney($summary['income_budgeted_categories']) }} income
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-5">
            <div class="card-panel h-100">
                <h3 class="h6 fw-semibold mb-3">Monthly targets</h3>
                <form method="POST" action="{{ route('budgets.plan.update') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="month" value="{{ $monthKey }}">
                    <div class="mb-3">
                        <label for="name" class="form-label">Plan name</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $plan->name) }}" maxlength="255">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label for="income_goal" class="form-label">Income goal</label>
                            <input type="number" step="0.01" min="0" id="income_goal" name="income_goal" class="form-control" value="{{ old('income_goal', $plan->income_goal) }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="expense_limit" class="form-label">Expense limit</label>
                            <input type="number" step="0.01" min="0" id="expense_limit" name="expense_limit" class="form-control" value="{{ old('expense_limit', $plan->expense_limit) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3" maxlength="2000">{{ old('notes', $plan->notes) }}</textarea>
                    </div>
                    <button type="submit" class="btn-primary-app">Save targets</button>
                </form>
            </div>
        </div>
        <div class="col-12 col-lg-7">
            <div class="card-panel h-100">
                <h3 class="h6 fw-semibold mb-3">Add category budget</h3>
                @if ($available_categories->isEmpty())
                    <p class="text-secondary small mb-0">All eligible categories already have a budget line for this month, or you have no categories yet. <a href="{{ route('categories.create') }}">Create a category</a>.</p>
                @else
                    <form method="POST" action="{{ route('budgets.items.store') }}" class="row g-3 align-items-end">
                        @csrf
                        <input type="hidden" name="month" value="{{ $monthKey }}">
                        <div class="col-12 col-md-7">
                            <label for="category_id" class="form-label">Category</label>
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select category</option>
                                @foreach ($available_categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                        {{ $category->name }} ({{ $category->type->label() }})
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" min="0.01" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Add</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @include('budgets._lines-section', [
        'title' => 'Expense budgets',
        'lines' => $expense_lines,
        'unbudgeted' => $unbudgeted_expense,
        'variant' => 'danger',
        'monthKey' => $monthKey,
        'emptyHint' => 'Add expense category budgets to track spending against each category.',
    ])

    @include('budgets._lines-section', [
        'title' => 'Income budgets',
        'lines' => $income_lines,
        'unbudgeted' => $unbudgeted_income,
        'variant' => 'success',
        'monthKey' => $monthKey,
        'emptyHint' => 'Add income category budgets to track earnings against each category.',
    ])
</x-user-layout>
