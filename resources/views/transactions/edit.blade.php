<x-user-layout>
    <x-slot name="header">Edit transaction</x-slot>
    <x-slot name="subheader">Update transaction details.</x-slot>

    <div class="grid grid-cols-12 gap-4 justify-center">
        <div class="col-span-12 lg:col-span-8">
            @include('transactions._form', ['transaction' => $transaction])
        </div>
    </div>
</x-user-layout>
