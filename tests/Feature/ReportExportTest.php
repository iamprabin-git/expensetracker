<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteContent();
    }

    public function test_report_csv_export_returns_download(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reports.export', [
            'report' => 'trial-balance',
            'format' => 'csv',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename=', (string) $response->headers->get('content-disposition'));
    }

    public function test_report_xlsx_export_returns_download(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reports.export', [
            'report' => 'transaction-statement',
            'format' => 'xlsx',
            'from_date' => now()->subMonth()->toDateString(),
            'to_date' => now()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
    }

    public function test_transactions_csv_export_includes_records(): void
    {
        $user = User::factory()->create();

        Transaction::query()->create([
            'user_id' => $user->id,
            'type' => TransactionType::Income,
            'title' => 'Salary',
            'amount' => 1500,
            'transaction_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get(route('transactions.export', ['format' => 'csv']));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Salary', $response->streamedContent());
    }

    public function test_statement_export_redirects_to_report_export(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('transactions.statement.export', [
            'format' => 'csv',
            'from_date' => '2026-01-01',
        ]));

        $response->assertRedirect(route('reports.export', [
            'report' => 'transaction-statement',
            'format' => 'csv',
            'from_date' => '2026-01-01',
        ]));
    }

    public function test_invalid_export_format_returns_not_found(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('reports.export', ['report' => 'trial-balance', 'format' => 'pdf']))
            ->assertNotFound();
    }
}
