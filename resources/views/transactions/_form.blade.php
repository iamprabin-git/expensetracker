@php
    $isEdit = filled($transaction);
    $action = $isEdit ? route('transactions.update', $transaction) : route('transactions.store');
    $defaultType = old('type', $transaction?->type?->value ?? request('type', 'expense'));
@endphp

<div class="card-panel">
    <form method="POST" action="{{ $action }}" class="row g-3">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="col-12 col-md-6">
            <label class="label-app" for="type">Type</label>
            <select name="type" id="type" class="input-app form-select @error('type') is-invalid @enderror" required>
                <option value="income" @selected($defaultType === 'income')>Income</option>
                <option value="expense" @selected($defaultType === 'expense')>Expense</option>
            </select>
            @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="label-app" for="transaction_date">Date</label>
            <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', $transaction?->transaction_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="input-app form-control @error('transaction_date') is-invalid @enderror" required>
            @error('transaction_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label class="label-app" for="title">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $transaction?->title) }}" class="input-app form-control @error('title') is-invalid @enderror" required>
            @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="label-app" for="amount">Amount</label>
            <input type="number" step="0.01" min="0.01" name="amount" id="amount" value="{{ old('amount', $transaction?->amount) }}" class="input-app form-control @error('amount') is-invalid @enderror" required>
            @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="label-app" for="category_id">Category</label>
            <select name="category_id" id="category_id" class="input-app form-select @error('category_id') is-invalid @enderror">
                <option value="">No category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $transaction?->category_id) == $category->id)>
                        {{ $category->name }} ({{ $category->type->label() }})
                    </option>
                @endforeach
            </select>
            @error('category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label class="label-app" for="description">Description</label>
            <textarea name="description" id="description" rows="3" class="input-app form-control @error('description') is-invalid @enderror">{{ old('description', $transaction?->description) }}</textarea>
            @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 d-flex flex-wrap gap-2">
            <button type="submit" class="btn-primary-app">{{ $isEdit ? 'Update' : 'Save' }} transaction</button>
            <a href="{{ route('transactions.index') }}" class="btn-secondary-app">Cancel</a>
        </div>
    </form>
</div>
