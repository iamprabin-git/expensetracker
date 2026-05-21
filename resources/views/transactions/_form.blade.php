@php
    $isEdit = filled($transaction);
    $action = $isEdit ? route('transactions.update', $transaction) : route('transactions.store');
    $defaultType = old('type', $transaction?->type?->value ?? request('type', 'expense'));
@endphp

<div class="card-panel">
    <form method="POST" action="{{ $action }}" class="grid grid-cols-12 gap-3">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="col-span-12 md:col-span-6">
            <label class="label-app" for="type">Type</label>
            <select name="type" id="type" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('type') is-invalid @enderror" required>
                @foreach (\App\Enums\TransactionType::cases() as $type)
                    <option value="{{ $type->value }}" @selected($defaultType === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
            @error('type')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 md:col-span-6">
            <label class="label-app" for="transaction_date">Date</label>
            <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', $transaction?->transaction_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('transaction_date') is-invalid @enderror" required>
            @error('transaction_date')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12">
            <label class="label-app" for="title">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $transaction?->title) }}" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('title') is-invalid @enderror" required>
            @error('title')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 md:col-span-6">
            <label class="label-app" for="amount">Amount</label>
            <input type="number" step="0.01" min="0.01" name="amount" id="amount" value="{{ old('amount', $transaction?->amount) }}" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('amount') is-invalid @enderror" required>
            @error('amount')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 md:col-span-6">
            <label class="label-app" for="category_id">Category</label>
            <select name="category_id" id="category_id" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('category_id') is-invalid @enderror">
                <option value="">No category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $transaction?->category_id) == $category->id)>
                        {{ $category->name }} ({{ $category->type->label() }})
                    </option>
                @endforeach
            </select>
            @error('category_id')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12">
            <label class="label-app" for="description">Description</label>
            <textarea name="description" id="description" rows="3" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('description') is-invalid @enderror">{{ old('description', $transaction?->description) }}</textarea>
            @error('description')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 flex flex-wrap gap-2">
            <x-ui.button type="submit">{{ $isEdit ? 'Update' : 'Save' }} transaction</x-ui.button>
            <x-ui.button variant="outline" href="{{ route('transactions.index') }}">Cancel</x-ui.button>
        </div>
    </form>
</div>
