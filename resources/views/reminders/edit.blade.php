<x-user-layout>
    <x-slot name="header">Edit reminder</x-slot>
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            @include('reminders._form', ['reminder' => $reminder])
        </div>
    </div>
</x-user-layout>
