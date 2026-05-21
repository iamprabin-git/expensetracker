@php
    $isEdit = isset($category) && $category;
    $action = $isEdit ? route('categories.update', $category) : route('categories.store');
@endphp

<div class="card-panel">
    <form method="POST" action="{{ $action }}" class="grid grid-cols-12 gap-3">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="col-span-12">
            <label class="label-app" for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $category?->name ?? '') }}" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('name') is-invalid @enderror" required>
            @error('name')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 md:col-span-6">
            <label class="label-app" for="type">Type</label>
            <select name="type" id="type" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('type') is-invalid @enderror" required>
                @foreach (\App\Enums\CategoryType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(old('type', $category?->type?->value ?? 'expense') === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
            @error('type')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 md:col-span-6">
            <label class="label-app" for="color">Color</label>
            <input type="color" name="color" id="color" value="{{ old('color', $category?->color ?? '#6366f1') }}" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs-color w-full @error('color') is-invalid @enderror">
            @error('color')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 flex gap-2">
            <x-ui.button type="submit">{{ $isEdit ? 'Update' : 'Create' }}</x-ui.button>
            <x-ui.button variant="outline" href="{{ route('categories.index') }}">Cancel</x-ui.button>
        </div>
    </form>
</div>

