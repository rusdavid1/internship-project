<?php

declare(strict_types=1);

namespace App\Import;

use App\Entity\Programme;
use App\Exception\InvalidFileException;

class ImportCsv
{
    public function getContentFromCsv(string $csvPath, string $openMode, string $csvSeparator): array
    {
        if (!filesize($csvPath) || !file_exists($csvPath)) {
            throw new InvalidFileException();
        }

        $handler = fopen($csvPath, $openMode);

        $csvArray = [];

        fgetcsv($handler);
        while (($data = fgetcsv($handler, null, $csvSeparator, "\"", "\"")) !== false) {
            $csvArray[] = explode(',', $data[0]);
        }
        fclose($handler);

        return $csvArray;
    }

    public function putFailedContentInCsv(string $csvPath, string $openMode, array $failedItems): void
    {
        if (!filesize($csvPath) || !file_exists($csvPath)) {
            throw new InvalidFileException();
        }

        $handlerFail = fopen($csvPath, $openMode);
        foreach ($failedItems as $failedItem) {
            fputcsv($handlerFail, $failedItem);
        }
        fclose($handlerFail);
    }

    public function getInvalidCsvLines(Programme $programme): bool
    {
        if (
            $programme->getStartDate()->getTimestamp() > $programme->getEndDate()->getTimestamp() ||
            ($programme->getEndDate()->getTimestamp() - $programme->getStartDate()->getTimestamp()) < 900 ||
            ($programme->getEndDate()->getTimestamp() - $programme->getStartDate()->getTimestamp()) > 21_600
        ) {
            return true;
        }

        return false;
    }
}
