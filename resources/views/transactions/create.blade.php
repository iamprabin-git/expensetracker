<x-user-layout>
    <x-slot name="header">Add transaction</x-slot>
    <x-slot name="subheader">Record income, expense, asset, or liability entries.</x-slot>

    <div class="grid grid-cols-12 gap-4 justify-center">
        <div class="col-span-12 lg:col-span-8">
            @include('transactions._form', ['transaction' => null])
        </div>
    </div>
</x-user-layout>
