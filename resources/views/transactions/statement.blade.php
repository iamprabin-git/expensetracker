@extends('layouts.statement')

@section('title', 'Transaction statement')

@section('content')
    @include('statements.partials.document', [
        'user' => $user,
        'transactions' => $transactions,
        'totals' => $totals,
        'periodLabel' => $periodLabel,
        'filters' => $filters,
        'filterAction' => route('transactions.statement'),
    ])
@endsection
