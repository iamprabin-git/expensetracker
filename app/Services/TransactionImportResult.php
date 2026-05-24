<?php

namespace App\Services;

class TransactionImportResult
{
    /**
     * @param  list<string>  $errors
     */
    public function __construct(
        public int $imported = 0,
        public int $skipped = 0,
        public array $errors = [],
    ) {}

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    public function totalProcessed(): int
    {
        return $this->imported + $this->skipped;
    }
}
