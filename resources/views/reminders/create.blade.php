<x-user-layout>
    <x-slot name="header">Create reminder</x-slot>
    <x-slot name="subheader">Set a date, time, and optional amount — we will email you when it is due.</x-slot>
    <div class="grid grid-cols-12 gap-4 justify-center">
        <div class="col-span-12 lg:col-span-8">
            @include('reminders._form')
        </div>
    </div>
</x-user-layout>
