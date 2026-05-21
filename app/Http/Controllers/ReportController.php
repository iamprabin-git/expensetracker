<?php

namespace App\Http\Controllers;

use App\Services\FinancialReportBuilder;
use App\Services\ReportExportService;
use App\Support\TabularExporter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const REPORTS = [
        'trial-balance' => [
            'title' => 'Trial Balance',
            'view' => 'reports.trial-balance',
            'pdf' => 'reports.pdf.trial-balance',
        ],
        'profit-loss' => [
            'title' => 'Profit & Loss',
            'view' => 'reports.profit-loss',
            'pdf' => 'reports.pdf.profit-loss',
        ],
        'balance-sheet' => [
            'title' => 'Balance Sheet',
            'view' => 'reports.balance-sheet',
            'pdf' => 'reports.pdf.balance-sheet',
        ],
        'cash-flow' => [
            'title' => 'Cash Flow Statement',
            'view' => 'reports.cash-flow',
            'pdf' => 'reports.pdf.cash-flow',
        ],
        'transaction-statement' => [
            'title' => 'Transaction Statement',
            'view' => 'reports.transaction-statement',
            'pdf' => 'reports.pdf.transaction-statement',
        ],
    ];

    public function index(): View
    {
        return view('reports.index');
    }

    public function show(Request $request, string $report): View
    {
        $config = $this->config($report);
        $builder = $this->builder($request);

        return view($config['view'], $this->viewData($request, $report, $builder));
    }

    public function pdf(Request $request, string $report): Response
    {
        $config = $this->config($report);
        $builder = $this->builder($request);
        $data = $this->viewData($request, $report, $builder);

        $filename = str_replace(' ', '-', strtolower($config['title'])).'-'.now()->format('Y-m-d').'.pdf';

        $pdf = Pdf::loadView($config['pdf'], $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    public function export(Request $request, string $report, string $format): StreamedResponse
    {
        abort_unless(in_array($format, ['csv', 'xlsx'], true), 404);

        $config = $this->config($report);
        $builder = $this->builder($request);
        $exporter = new ReportExportService(
            $request->user(),
            $builder,
            $report,
            $config['title'],
        );
        $dataset = $exporter->dataset();
        $extension = $format === 'xlsx' ? 'xlsx' : 'csv';
        $filename = $exporter->filename($extension);

        return match ($format) {
            'csv' => TabularExporter::downloadCsv($filename, $dataset['headers'], $dataset['rows']),
            'xlsx' => TabularExporter::downloadXlsx($filename, $dataset['headers'], $dataset['rows']),
        };
    }

    private function builder(Request $request): FinancialReportBuilder
    {
        $this->validateFilters($request);

        return FinancialReportBuilder::fromRequest($request->user(), $request->all());
    }

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);
    }

    private function config(string $report): array
    {
        abort_unless(isset(self::REPORTS[$report]), 404);

        return self::REPORTS[$report];
    }

    private function viewData(Request $request, string $report, FinancialReportBuilder $builder): array
    {
        $config = self::REPORTS[$report];

        $data = [
            'user' => $request->user(),
            'builder' => $builder,
            'reportKey' => $report,
            'reportTitle' => $config['title'],
            'periodLabel' => $builder->periodLabel(),
            'filters' => $builder->filters(),
            'generatedAt' => now(),
            'pdfRoute' => 'reports.pdf',
        ];

        return match ($report) {
            'trial-balance' => array_merge($data, ['report' => $builder->trialBalance()]),
            'profit-loss' => array_merge($data, ['report' => $builder->profitAndLoss()]),
            'balance-sheet' => array_merge($data, ['report' => $builder->balanceSheet()]),
            'cash-flow' => array_merge($data, ['report' => $builder->cashFlow()]),
            'transaction-statement' => (function () use ($data, $builder) {
                $statement = $builder->transactionStatement();

                return array_merge($data, [
                    'report' => $statement,
                    'transactions' => $statement['transactions'],
                    'totals' => $statement['totals'],
                ]);
            })(),
        };
    }
}
