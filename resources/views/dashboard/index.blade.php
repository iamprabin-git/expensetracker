<x-user-layout>
    <x-slot name="header">Dashboard</x-slot>
    <x-slot name="subheader">Track your income, expenses, and balance at a glance.</x-slot>
    <x-slot name="headerActions">
        <x-ui.button href="{{ route('transactions.create') }}">Add transaction</x-ui.button>
    </x-slot>

    <div class="grid grid-cols-12 gap-3 md:gap-4 mb-4">
        <div class="col-span-12 sm:col-span-6 xl:col-span-3">
            <div class="card-panel h-full">
                <p class="text-sm text-muted-foreground mb-1">Total income</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400"><x-money :amount="$income" /></p>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3">
            <div class="card-panel h-full">
                <p class="text-sm text-muted-foreground mb-1">Total expenses</p>
                <p class="text-2xl font-bold text-rose-600 dark:text-rose-400"><x-money :amount="$expense" /></p>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3">
            <div class="card-panel h-full">
                <p class="text-sm text-muted-foreground mb-1">Net balance</p>
                <p @class(['text-2xl font-bold', $balance >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400'])>
                    <x-money :amount="$balance" />
                </p>
            </div>
        </div>
        <div class="col-span-12 sm:col-span-6 xl:col-span-3">
            <div class="card-panel h-full">
                <p class="text-sm text-muted-foreground mb-1">This month</p>
                <p class="text-sm mb-0"><span class="text-emerald-600 dark:text-emerald-400">+<x-money :amount="$monthlyIncome" /></span> / <span class="text-rose-600 dark:text-rose-400">-<x-money :amount="$monthlyExpense" /></span></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 lg:col-span-8">
            <div class="card-panel">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-lg font-semibold mb-0">Recent transactions</h2>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400">View all</a>
                </div>

                <div class="overflow-x-auto table-scroll-touch">
                    <table class="w-full align-middle mb-0 table-mobile-stack">
                        <thead>
                            <tr class="text-muted-foreground">
                                <th>Title</th>
                                <th class="hidden md:table-cell">Category</th>
                                <th>Date</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="font-medium" data-label="Title">{{ $transaction->title }}</td>
                                    <td class="hidden md:table-cell" data-label="Category">
                                        @if ($transaction->category)
                                            <span class="inline-flex items-center gap-1.5 rounded-md border border-border bg-muted/40 px-2 py-0.5 text-xs font-medium text-foreground">
                                                <x-category-icon :category="$transaction->category" class="size-6 [&_svg]:size-3" />
                                                {{ $transaction->category->name }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td data-label="Date">{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                    <td @class(['text-right font-semibold', $transaction->amountColorClass()]) data-label="Amount">
                                        {{ $transaction->amountPrefix() }}<x-money :amount="$transaction->amount" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted-foreground py-4">No transactions yet. Add your first one!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="card-panel h-full">
                <h2 class="text-lg font-semibold mb-3">Quick actions</h2>
                <div class="grid gap-2">
                    <x-ui.button variant="default" class="bg-emerald-600 hover:bg-emerald-600/90" href="{{ route('transactions.create', ['type' => 'income']) }}">Record income</x-ui.button>
                    <x-ui.button variant="destructive" href="{{ route('transactions.create', ['type' => 'expense']) }}">Record expense</x-ui.button>
                    <x-ui.button variant="outline" href="{{ route('categories.create') }}">Manage categories</x-ui.button>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>
