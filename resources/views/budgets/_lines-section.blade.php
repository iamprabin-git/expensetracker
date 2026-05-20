@php
    $user = auth()->user();
@endphp

<div class="card-panel mb-4">
    <h3 class="h6 fw-semibold mb-3">{{ $title }}</h3>

    @if ($lines->isEmpty() && $unbudgeted->isEmpty())
        <p class="text-secondary small mb-0">{{ $emptyHint }}</p>
    @else
        <div class="table-responsive table-scroll-touch">
            <table class="table table-hover align-middle mb-0 table-mobile-stack">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th class="text-end">Budget</th>
                        <th class="text-end">Actual</th>
                        <th class="text-end">Remaining</th>
                        <th style="min-width: 8rem">Progress</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lines as $line)
                        <tr class="{{ $line['over_budget'] ? 'table-warning' : '' }}">
                            <td data-label="Category">
                                <span class="d-inline-block rounded-circle me-2 align-middle" style="width:10px;height:10px;background:{{ $line['category']->color ?? '#64748b' }}"></span>
                                {{ $line['category']->name }}
                                <span class="badge {{ $line['category']->type->badgeClass() }} ms-1">{{ $line['category']->type->label() }}</span>
                            </td>
                            <td class="text-end" data-label="Budget">
                                <form method="POST" action="{{ route('budgets.items.update', $line['item']) }}" class="d-inline-flex gap-1 justify-content-end align-items-center budget-inline-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="month" value="{{ $monthKey }}">
                                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ $line['budget'] }}" class="form-control form-control-sm text-end" style="max-width:7rem">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Save</button>
                                </form>
                            </td>
                            <td class="text-end fw-semibold text-{{ $variant }}" data-label="Actual">{{ $user->formatMoney($line['actual']) }}</td>
                            <td class="text-end {{ $line['remaining'] < 0 ? 'text-danger' : '' }}" data-label="Remaining">
                                {{ $user->formatMoney($line['remaining']) }}
                            </td>
                            <td data-label="Progress">
                                @include('budgets._meter', [
                                    'percent' => $line['percent'],
                                    'status' => $line['status'],
                                    'variant' => $variant,
                                    'hideLabel' => true,
                                ])
                                <span class="small text-secondary">{{ round($line['percent'], 1) }}%</span>
                            </td>
                            <td class="text-end" data-label="">
                                <form method="POST" action="{{ route('budgets.items.destroy', $line['item']) }}" onsubmit="return confirm('Remove this budget line?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($unbudgeted->isNotEmpty())
            <div class="mt-4 pt-3 border-top">
                <p class="small fw-semibold mb-2">Spending without a budget line</p>
                <ul class="list-unstyled small mb-0">
                    @foreach ($unbudgeted as $row)
                        <li class="d-flex justify-content-between py-1">
                            <span>{{ $row['category']->name }}</span>
                            <span class="text-{{ $variant }} fw-semibold">{{ $user->formatMoney($row['actual']) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
</div>
