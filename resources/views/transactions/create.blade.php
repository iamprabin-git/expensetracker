<x-user-layout>
    <x-slot name="header">Add transaction</x-slot>
    <x-slot name="subheader">Record a new income or expense entry.</x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            @include('transactions._form', ['transaction' => null])
        </div>
    </div>
</x-user-layout>
