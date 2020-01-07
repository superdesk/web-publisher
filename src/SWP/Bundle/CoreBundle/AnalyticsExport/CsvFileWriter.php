<?php

namespace SWP\Bundle\CoreBundle\AnalyticsExport;

class CsvFileWriter
{
    public function write(string $fileSourcePath, array $data): void
    {
        $fp = fopen($fileSourcePath, 'w');

        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }
}
