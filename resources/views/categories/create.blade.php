<x-user-layout>
    <x-slot name="header">Create category</x-slot>
    <div class="grid grid-cols-12 gap-4 justify-center">
        <div class="col-span-12 md:col-span-8">
            @include('categories._form')
        </div>
    </div>
</x-user-layout>
