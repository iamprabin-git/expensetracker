<x-user-layout>
    <x-slot name="header">Edit category</x-slot>
    <div class="grid grid-cols-12 gap-4 justify-center">
        <div class="col-span-12 md:col-span-8">
            @include('categories._form', ['category' => $category])
        </div>
    </div>
</x-user-layout>

