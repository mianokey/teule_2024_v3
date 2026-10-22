<?php

namespace App\Services\Reports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    /**
     * Export report rows to a genuine Excel XLSX or CSV file.
     *
     * @param array<int, string> $headings
     * @param iterable<array<int, mixed>> $rows
     */
    public function download(
        array $headings,
        iterable $rows,
        string $filename,
        string $format
    ): StreamedResponse {
        $format = strtolower($format);

        if (!in_array($format, ['xlsx', 'csv'], true)) {
            abort(422, 'Unsupported export format.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray($headings, null, 'A1');

        $rowNumber = 2;

        foreach ($rows as $row) {
            $sheet->fromArray(array_values($row), null, 'A' . $rowNumber);
            $rowNumber++;
        }

        $sheet->freezePane('A2');
        $sheet->getStyle('1:1')->getFont()->setBold(true);

        foreach (range(1, max(1, count($headings))) as $column) {
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }

        $filename = pathinfo($filename, PATHINFO_FILENAME)
            . '.'
            . $format;

        return response()->streamDownload(function () use ($spreadsheet, $format) {
            if ($format === 'xlsx') {
                (new Xlsx($spreadsheet))->save('php://output');
            } else {
                $writer = new Csv($spreadsheet);
                $writer->setDelimiter(',');
                $writer->setEnclosure('"');
                $writer->setUseBOM(true);
                $writer->save('php://output');
            }

            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => $format === 'xlsx'
                ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                : 'text/csv; charset=UTF-8',
        ]);
    }
}
