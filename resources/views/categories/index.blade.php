<x-user-layout>
    <x-slot name="header">Categories</x-slot>
    <x-slot name="subheader">Organize your transactions with custom categories.</x-slot>
    <x-slot name="headerActions">
        <x-ui.button href="{{ route('categories.create') }}">New category</x-ui.button>
    </x-slot>

    <div class="grid grid-cols-12 gap-3">
        @forelse ($categories as $category)
            <div class="col-span-12 sm:col-span-6 col-lg-4">
                <div class="card-panel h-full">
                    <div class="flex align-items-start justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <x-category-icon :category="$category" class="size-9" />
                            <h3 class="h6 font-semibold mb-0">{{ $category->name }}</h3>
                        </div>
                        <span class="badge {{ $category->type->badgeClass() }}">{{ $category->type->label() }}</span>
                    </div>
                    @if ($category->user_id)
                        <div class="flex flex-wrap gap-2 mt-3 categories-index-actions">
                            <x-ui.button variant="outline" size="sm" href="{{ route('categories.edit', $category) }}">Edit</x-ui.button>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="destructive" size="sm">Delete</x-ui.button>
                            </form>
                        </div>
                    @else
                        <p class="text-muted-foreground small mb-0">System category (read-only)</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-12">
                <div class="card-panel text-center text-muted-foreground py-5">No categories yet.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $categories->links() }}</div>
</x-user-layout>

