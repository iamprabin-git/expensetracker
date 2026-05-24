<?php

namespace App\Support;

use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\ReaderInterface;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class TabularImporter
{
    /**
     * @return array{headers: list<string>, rows: list<list<mixed>>}
     */
    public static function read(string $path, string $extension): array
    {
        $reader = self::readerForExtension($extension);
        $reader->open($path);

        try {
            $sheet = $reader->getSheetIterator()->current();
            $rowIterator = $sheet->getRowIterator();

            $headers = [];
            $rows = [];
            $isFirst = true;

            foreach ($rowIterator as $row) {
                if ($row === null || $row->isEmpty()) {
                    continue;
                }

                $values = self::normalizeRow($row->toArray());

                if ($isFirst) {
                    $headers = array_map(
                        fn ($value) => self::normalizeHeader((string) $value),
                        $values
                    );
                    $isFirst = false;

                    continue;
                }

                $rows[] = $values;
            }
        } finally {
            $reader->close();
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    /**
     * @param  list<mixed>  $values
     * @return list<mixed>
     */
    private static function normalizeRow(array $values): array
    {
        return array_map(function (mixed $value): mixed {
            if ($value instanceof \DateTimeInterface) {
                return $value->format('Y-m-d');
            }

            if (is_string($value)) {
                return trim($value);
            }

            return $value;
        }, $values);
    }

    private static function normalizeHeader(string $header): string
    {
        $header = trim($header);

        if (str_starts_with($header, "\xEF\xBB\xBF")) {
            $header = substr($header, 3);
        }

        return strtolower($header);
    }

    public static function readerForExtension(string $extension): ReaderInterface
    {
        return match (strtolower($extension)) {
            'csv', 'txt' => new CsvReader,
            'xlsx' => new XlsxReader,
            default => throw new \InvalidArgumentException(
                'Unsupported spreadsheet type. Use a .csv or .xlsx file.'
            ),
        };
    }
}
