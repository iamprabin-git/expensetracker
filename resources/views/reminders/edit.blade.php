<x-user-layout>
    <x-slot name="header">Edit reminder</x-slot>
    <div class="grid grid-cols-12 gap-4 justify-center">
        <div class="col-span-12 lg:col-span-8">
            @include('reminders._form', ['reminder' => $reminder])
        </div>
    </div>
</x-user-layout>
