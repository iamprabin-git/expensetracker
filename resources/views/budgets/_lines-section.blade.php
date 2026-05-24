@php
    $user = auth()->user();
@endphp

<div class="card-panel mb-4">
    <h3 class="h6 font-semibold mb-3">{{ $title }}</h3>

    @if ($lines->isEmpty() && $unbudgeted->isEmpty())
        <p class="text-muted-foreground small mb-0">{{ $emptyHint }}</p>
    @else
        <div class="overflow-x-auto table-scroll-touch">
            <table class="w-full align-middle mb-0 table-mobile-stack">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th class="text-right">Budget</th>
                        <th class="text-right">Actual</th>
                        <th class="text-right">Remaining</th>
                        <th style="min-width: 8rem">Progress</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lines as $line)
                        <tr class="{{ $line['over_budget'] ? 'table-warning' : '' }}">
                            <td data-label="Category">
                                @if (! empty($line['category']))
                                    <x-category-icon :category="$line['category']" class="size-6 me-2 align-middle inline-flex [&_svg]:size-3" />
                                @endif
                                {{ $line['category']->name }}
                                <span class="badge {{ $line['category']->type->badgeClass() }} ms-1">{{ $line['category']->type->label() }}</span>
                            </td>
                            <td class="text-right" data-label="Budget">
                                <form method="POST" action="{{ route('budgets.items.update', $line['item']) }}" class="inline-flex gap-1 justify-content-end items-center budget-inline-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="month" value="{{ $monthKey }}">
                                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ $line['budget'] }}" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs-sm text-right" style="max-width:7rem">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Save</button>
                                </form>
                            </td>
                            <td class="text-right font-semibold text-{{ $variant }}" data-label="Actual">{{ $user->formatMoney($line['actual']) }}</td>
                            <td class="text-right {{ $line['remaining'] < 0 ? 'text-destructive' : '' }}" data-label="Remaining">
                                {{ $user->formatMoney($line['remaining']) }}
                            </td>
                            <td data-label="Progress">
                                @include('budgets._meter', [
                                    'percent' => $line['percent'],
                                    'status' => $line['status'],
                                    'variant' => $variant,
                                    'hideLabel' => true,
                                ])
                                <span class="text-sm text-muted-foreground">{{ round($line['percent'], 1) }}%</span>
                            </td>
                            <td class="text-right" data-label="">
                                <form method="POST" action="{{ route('budgets.items.destroy', $line['item']) }}" onsubmit="return confirm('Remove this budget line?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="destructive" size="sm">Remove</x-ui.button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($unbudgeted->isNotEmpty())
            <div class="mt-4 pt-3 border-top">
                <p class="text-sm font-semibold mb-2">Spending without a budget line</p>
                <ul class="list-none small mb-0">
                    @foreach ($unbudgeted as $row)
                        <li class="flex justify-between py-1">
                            <span>{{ $row['category']->name }}</span>
                            <span class="text-{{ $variant }} font-semibold">{{ $user->formatMoney($row['actual']) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
</div>
