<?php

namespace App\Http\Controllers;

use DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BankReconciliationTemplateController extends Controller
{
    public function downloadTemplate(): BinaryFileResponse
    {
        // Create new spreadsheet
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['Tanggal', 'Keterangan', 'Debit', 'Credit'];

        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
        }

        // Add instruction row
        $instructions = ['Format: dd/mm/yyyy', 'Deskripsi transaksi', 'Jumlah keluar', 'Jumlah masuk'];
        foreach ($instructions as $index => $instruction) {
            $sheet->setCellValueByColumnAndRow($index + 1, 2, $instruction);
        }

        // Add sample data dengan format tanggal yang benar
        $sampleData = [
            ['15/10/2024', 'Transfer Masuk dari PT ABC', 0, 1000000],
            ['15/10/2024', 'Pembayaran Supplier XYZ', 500000, 0],
            ['16/10/2024', 'Biaya Admin Bank', 15000, 0],
            ['16/10/2024', 'Bunga Deposito', 0, 25000],
        ];

        foreach ($sampleData as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                if ($colIndex === 0) { // Kolom tanggal
                    // Set nilai sebagai tanggal Excel
                    $dateValue = Date::PHPToExcel(
                        DateTime::createFromFormat('d/m/Y', $value)
                    );
                    $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 3, $dateValue);
                } else {
                    $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 3, $value);
                }
            }
        }

        // Style the header
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Style instruction row
        $sheet->getStyle('A2:D2')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('A2:D2')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF0F0F0');

        // Auto-size columns
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set date format for tanggal column (dd/mm/yyyy)
        $sheet->getStyle('A:A')->getNumberFormat()
            ->setFormatCode('dd/mm/yyyy');

        // Set number format for amount columns
        $sheet->getStyle('C:D')->getNumberFormat()
            ->setFormatCode('#,##0');

        // Create file
        $fileName = 'Bank_Reconciliation_Template_'.date('Y-m-d').'.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'bank_reconciliation_');

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }
}
