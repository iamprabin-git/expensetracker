<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Category;
use App\Models\User;
use App\Support\TabularExporter;
use App\Support\TabularImporter;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionImportService
{
    public const MAX_ROWS = 2000;

    /** @var array<string, string> */
    private const COLUMN_ALIASES = [
        'date' => 'date',
        'transaction date' => 'date',
        'transaction_date' => 'date',
        'title' => 'title',
        'description' => 'description',
        'desc' => 'description',
        'notes' => 'description',
        'type' => 'type',
        'category' => 'category',
        'amount' => 'amount',
        'receipt' => 'receipt',
    ];

    public function templateDownload(): StreamedResponse
    {
        $headers = ['Date', 'Title', 'Description', 'Type', 'Category', 'Amount'];
        $rows = [
            [
                now()->toDateString(),
                'Grocery shopping',
                'Weekly groceries',
                'expense',
                '',
                '85.50',
            ],
            [
                now()->toDateString(),
                'Salary',
                'Monthly pay',
                'income',
                '',
                '2500.00',
            ],
        ];

        return TabularExporter::downloadXlsx('transaction-import-template.xlsx', $headers, $rows);
    }

    public function import(User $user, UploadedFile $file): TransactionImportResult
    {
        $path = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        if ($path === false) {
            return new TransactionImportResult(errors: ['Could not read the uploaded file.']);
        }

        if (! in_array($extension, ['csv', 'txt', 'xlsx'], true)) {
            return new TransactionImportResult(errors: ['The file must be a .csv or .xlsx spreadsheet.']);
        }

        try {
            $sheet = TabularImporter::read($path, $extension);
        } catch (\Throwable $exception) {
            return new TransactionImportResult(errors: ['Could not read spreadsheet: '.$exception->getMessage()]);
        }

        $columnMap = $this->resolveColumnMap($sheet['headers']);

        if (! isset($columnMap['date'], $columnMap['title'], $columnMap['type'], $columnMap['amount'])) {
            return new TransactionImportResult(errors: [
                'Missing required columns. Your file must include: Date, Title, Type, and Amount.',
            ]);
        }

        $categories = Category::forUser($user)
            ->get()
            ->keyBy(fn (Category $category) => strtolower($category->name));

        $result = new TransactionImportResult;
        $rows = $sheet['rows'];

        if (count($rows) > self::MAX_ROWS) {
            return new TransactionImportResult(errors: [
                'Too many rows ('.count($rows).'). Maximum allowed is '.self::MAX_ROWS.'.',
            ]);
        }

        DB::transaction(function () use ($user, $rows, $columnMap, $categories, &$result): void {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                if ($this->isBlankRow($row)) {
                    continue;
                }

                $parsed = $this->parseRow($row, $columnMap, $categories, $rowNumber);

                if (isset($parsed['error'])) {
                    $result->skipped++;
                    $result->errors[] = $parsed['error'];

                    continue;
                }

                $user->transactions()->create($parsed['data']);
                $result->imported++;
            }
        });

        return $result;
    }

    /**
     * @param  list<string>  $headers
     * @return array<string, int>
     */
    private function resolveColumnMap(array $headers): array
    {
        $map = [];

        foreach ($headers as $index => $header) {
            $key = self::COLUMN_ALIASES[$header] ?? null;

            if ($key !== null && ! isset($map[$key])) {
                $map[$key] = $index;
            }
        }

        return $map;
    }

    /**
     * @param  list<mixed>  $row
     * @param  array<string, int>  $columnMap
     * @param  Collection<string, Category>  $categories
     * @return array{data?: array<string, mixed>, error?: string}
     */
    private function parseRow(array $row, array $columnMap, $categories, int $rowNumber): array
    {
        $dateRaw = $this->cell($row, $columnMap, 'date');
        $title = trim((string) $this->cell($row, $columnMap, 'title'));
        $typeRaw = $this->cell($row, $columnMap, 'type');
        $amountRaw = $this->cell($row, $columnMap, 'amount');
        $description = trim((string) ($this->cell($row, $columnMap, 'description') ?? ''));
        $categoryName = trim((string) ($this->cell($row, $columnMap, 'category') ?? ''));

        if ($title === '') {
            return ['error' => "Row {$rowNumber}: Title is required."];
        }

        $date = $this->parseDate($dateRaw);
        if ($date === null) {
            return ['error' => "Row {$rowNumber}: Invalid or missing date."];
        }

        $type = $this->parseType($typeRaw);
        if ($type === null) {
            return ['error' => "Row {$rowNumber}: Type must be income, expense, asset, or liability."];
        }

        $amount = $this->parseAmount($amountRaw);
        if ($amount === null) {
            return ['error' => "Row {$rowNumber}: Invalid or missing amount."];
        }

        $categoryId = null;
        if ($categoryName !== '') {
            $category = $categories->get(strtolower($categoryName));

            if ($category === null) {
                return ['error' => "Row {$rowNumber}: Category \"{$categoryName}\" was not found."];
            }

            $categoryId = $category->id;
        }

        return [
            'data' => [
                'type' => $type,
                'title' => $title,
                'amount' => $amount,
                'category_id' => $categoryId,
                'description' => $description !== '' ? $description : null,
                'transaction_date' => $date,
            ],
        ];
    }

    /**
     * @param  list<mixed>  $row
     * @param  array<string, int>  $columnMap
     */
    private function cell(array $row, array $columnMap, string $key): mixed
    {
        if (! isset($columnMap[$key])) {
            return null;
        }

        return $row[$columnMap[$key]] ?? null;
    }

    /**
     * @param  list<mixed>  $row
     */
    private function isBlankRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value)) {
            $unix = ((int) $value - 25569) * 86400;

            return gmdate('Y-m-d', $unix);
        }

        $string = trim((string) $value);
        if ($string === '') {
            return null;
        }

        try {
            return Carbon::parse($string)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseType(mixed $value): ?TransactionType
    {
        $normalized = strtolower(trim((string) $value));

        if ($normalized === '') {
            return null;
        }

        foreach (TransactionType::cases() as $type) {
            if ($normalized === $type->value || $normalized === strtolower($type->label())) {
                return $type;
            }
        }

        return null;
    }

    private function parseAmount(mixed $value): ?float
    {
        if (is_int($value) || is_float($value)) {
            $amount = abs((float) $value);

            return $amount >= 0.01 ? round($amount, 2) : null;
        }

        $string = trim((string) $value);
        if ($string === '') {
            return null;
        }

        $digits = preg_replace('/[^\d.]/', '', str_replace(',', '', $string));

        if ($digits === '' || ! is_numeric($digits)) {
            return null;
        }

        $amount = round(abs((float) $digits), 2);

        if ($amount < 0.01) {
            return null;
        }

        return $amount;
    }
}
