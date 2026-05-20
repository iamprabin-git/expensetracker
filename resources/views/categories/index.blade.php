<x-user-layout>
    <x-slot name="header">Categories</x-slot>
    <x-slot name="subheader">Organize your transactions with custom categories.</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('categories.create') }}" class="btn-primary-app">New category</a>
    </x-slot>

    <div class="row g-3">
        @forelse ($categories as $category)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card-panel h-100">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-block" style="width:12px;height:12px;background:{{ $category->color }}"></span>
                            <h3 class="h6 fw-semibold mb-0">{{ $category->name }}</h3>
                        </div>
                        <span class="badge {{ $category->type->badgeClass() }}">{{ $category->type->label() }}</span>
                    </div>
                    @if ($category->user_id)
                        <div class="d-flex flex-wrap gap-2 mt-3 categories-index-actions">
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    @else
                        <p class="text-secondary small mb-0">System category (read-only)</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-panel text-center text-secondary py-5">No categories yet.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $categories->links() }}</div>
</x-user-layout>

