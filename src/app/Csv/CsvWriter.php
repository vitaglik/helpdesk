<?php

namespace App\Csv;

use http\Exception\RuntimeException;

class CsvWriter
{
    private $handle;

    public function __construct(string $filePath)
    {
        $this->handle = fopen($filePath, 'wb');

        if ($this->handle === false) {
            throw new RuntimeException('Cannot open file.');
        }
    }

    public function write(array $row): void
    {
        fputcsv($this->handle, $row, ';');
    }

    public function close(): void
    {
        fclose($this->handle);
    }
}