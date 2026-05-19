<x-user-layout>
    <x-slot name="header">Edit transaction</x-slot>
    <x-slot name="subheader">Update transaction details.</x-slot>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            @include('transactions._form', ['transaction' => $transaction])
        </div>
    </div>
</x-user-layout>
