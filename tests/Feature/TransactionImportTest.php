<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TransactionImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteContent();
    }

    public function test_import_template_downloads_xlsx(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('transactions.import.template'));

        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
    }

    public function test_csv_import_creates_transactions(): void
    {
        $user = User::factory()->create();

        $csv = implode("\n", [
            'Date,Title,Description,Type,Category,Amount',
            '2026-01-10,Coffee,,expense,,4.50',
            '2026-01-11,Bonus,,income,,1200',
        ]);

        $file = $this->csvUpload($csv);

        $response = $this->actingAs($user)->post(route('transactions.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('transactions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'title' => 'Coffee',
            'type' => TransactionType::Expense->value,
            'amount' => 4.50,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'title' => 'Bonus',
            'type' => TransactionType::Income->value,
            'amount' => 1200,
        ]);

        $this->assertSame(2, Transaction::query()->where('user_id', $user->id)->count());
    }

    public function test_import_reports_invalid_rows(): void
    {
        $user = User::factory()->create();

        $csv = implode("\n", [
            'Date,Title,Type,Amount',
            '2026-01-10,,expense,10',
            '2026-01-11,Valid lunch,expense,12.50',
        ]);

        $file = $this->csvUpload($csv);

        $response = $this->actingAs($user)->post(route('transactions.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('transactions.index'));
        $response->assertSessionHas('import_errors');

        $this->assertSame(1, Transaction::query()->where('user_id', $user->id)->count());
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'title' => 'Valid lunch',
        ]);
    }

    public function test_import_reads_file_when_temp_path_has_no_extension(): void
    {
        $user = User::factory()->create();

        $csv = implode("\n", [
            'Date,Title,Type,Amount',
            '2026-02-01,Test row,expense,9.99',
        ]);

        $path = storage_path('app/testing-import.tmp');
        file_put_contents($path, $csv);

        $file = new UploadedFile($path, 'transactions.csv', 'text/csv', null, true);

        $response = $this->actingAs($user)->post(route('transactions.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('transactions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'title' => 'Test row',
            'amount' => 9.99,
        ]);
    }

    private function csvUpload(string $content): UploadedFile
    {
        $path = storage_path('app/testing-import.csv');
        file_put_contents($path, $content);

        return new UploadedFile($path, 'transactions.csv', 'text/csv', null, true);
    }
}
