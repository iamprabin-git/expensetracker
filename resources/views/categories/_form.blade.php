@php
    $isEdit = isset($category) && $category;
    $action = $isEdit ? route('categories.update', $category) : route('categories.store');
@endphp

<div class="card-panel">
    <form method="POST" action="{{ $action }}" class="row g-3">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="col-12">
            <label class="label-app" for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $category?->name ?? '') }}" class="input-app form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="label-app" for="type">Type</label>
            <select name="type" id="type" class="input-app form-select @error('type') is-invalid @enderror" required>
                @foreach (\App\Enums\CategoryType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(old('type', $category?->type?->value ?? 'expense') === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
            @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="label-app" for="color">Color</label>
            <input type="color" name="color" id="color" value="{{ old('color', $category?->color ?? '#6366f1') }}" class="form-control form-control-color w-100 @error('color') is-invalid @enderror">
            @error('color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn-primary-app">{{ $isEdit ? 'Update' : 'Create' }}</button>
            <a href="{{ route('categories.index') }}" class="btn-secondary-app">Cancel</a>
        </div>
    </form>
</div>

