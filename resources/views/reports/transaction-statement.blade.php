@extends('layouts.report')

@section('title', $reportTitle)

@push('styles')
    @vite(['resources/css/statement.css'])
@endpush

@section('content')
    @include('reports.partials.filters')

    @include('statements.partials.document', [
        'user' => $user,
        'transactions' => $transactions,
        'totals' => $totals,
        'periodLabel' => $periodLabel,
        'generatedAt' => $generatedAt,
        'documentTitle' => $reportTitle,
    ])
@endsection
