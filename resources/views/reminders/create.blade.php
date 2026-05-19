<x-user-layout>
    <x-slot name="header">Create reminder</x-slot>
    <x-slot name="subheader">Set a date, time, and optional amount — we will email you when it is due.</x-slot>
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            @include('reminders._form')
        </div>
    </div>
</x-user-layout>
