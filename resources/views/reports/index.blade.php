<x-user-layout>
    <x-slot name="header">Reports</x-slot>
    <x-slot name="subheader">Financial statements from your transactions — view, print, or download as PDF.</x-slot>

    <div class="grid grid-cols-12 gap-4">
        @foreach ([
            ['key' => 'trial-balance', 'title' => 'Trial Balance', 'desc' => 'Debit and credit totals by account category with balanced totals.', 'icon' => '⚖️'],
            ['key' => 'profit-loss', 'title' => 'Profit & Loss', 'desc' => 'Revenue, expenses, and net profit or loss for the period.', 'icon' => '📈'],
            ['key' => 'balance-sheet', 'title' => 'Balance Sheet', 'desc' => 'Assets, liabilities, and equity as of the selected date.', 'icon' => '📋'],
            ['key' => 'cash-flow', 'title' => 'Cash Flow Statement', 'desc' => 'Cash inflows, outflows, and net change in cash.', 'icon' => '💵'],
            ['key' => 'transaction-statement', 'title' => 'Transaction Statement', 'desc' => 'Detailed list of every income and expense transaction.', 'icon' => '📄'],
        ] as $item)
            <div class="col-span-12 md:col-span-6 col-xl-4">
                <div class="card-panel h-full flex flex-col">
                    <div class="fs-2 mb-2" aria-hidden="true">{{ $item['icon'] }}</div>
                    <h2 class="text-lg font-semibold mb-2">{{ $item['title'] }}</h2>
                    <p class="text-muted-foreground small flex-1">{{ $item['desc'] }}</p>
                    <a href="{{ route('reports.show', $item['key']) }}" class="btn btn-sm site-btn-primary mt-3 self-start">Open report</a>
                </div>
            </div>
        @endforeach
    </div>
</x-user-layout>
