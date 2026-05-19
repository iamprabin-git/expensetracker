<x-user-layout>
    <x-slot name="header">Edit category</x-slot>
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            @include('categories._form', ['category' => $category])
        </div>
    </div>
</x-user-layout>

