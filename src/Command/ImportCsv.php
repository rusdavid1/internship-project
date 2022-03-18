<?php

declare(strict_types=1);

namespace App\Command;

class ImportCsv
{
    public function getContentFromCsv(string $csvPath, string $openMode, string $csvSeparator): array
    {
        $handler = fopen($csvPath, $openMode);

        $csvArray = [];

        fgetcsv($handler);
        while (($data = fgetcsv($handler, null, $csvSeparator)) !== false) {
            $csvArray[] = $data;
        }
        fclose($handler);

        return $csvArray;
    }

    public function putFailedContentInCsv(string $csvPath, string $openMode, array $failedItems): void
    {
        $handlerFail = fopen($csvPath, $openMode);
        foreach ($failedItems as $failedItem) {
            fputcsv($handlerFail, $failedItem);
        }
        fclose($handlerFail);
    }
}
