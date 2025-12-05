<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReconciliationExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Summary Sheet
        $sheets[] = new ReconciliationSummarySheet($this->data);

        // Matched Transactions Sheet
        $sheets[] = new ReconciliationMatchedSheet($this->data['matched']);

        // Unmatched App Transactions Sheet
        $sheets[] = new ReconciliationUnmatchedAppSheet($this->data['unmatched_app']);

        // Unmatched Bank Transactions Sheet
        $sheets[] = new ReconciliationUnmatchedBankSheet($this->data['unmatched_bank']);

        return $sheets;
    }
}

class ReconciliationSummarySheet implements FromArray, WithEvents, WithHeadings, WithStyles, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function headings(): array
    {
        return [
            'Keterangan',
            'Nilai',
        ];
    }

    public function array(): array
    {
        $statistics = $this->data['statistics'];
        $paymentMethod = $this->data['payment_method'];
        $period = $this->data['period'];
        $start = Carbon::parse($period['start'])->format('d/m/Y');
        $end = Carbon::parse($period['end'])->format('d/m/Y');

        return [
            ['Bank', $paymentMethod->bank_name],
            ['No. Rekening', $paymentMethod->no_rekening],
            ['Periode Mulai', $start],
            ['Periode Akhir', $end],
            ['', ''],
            ['STATISTIK REKONSILIASI', ''],
            ['Total Transaksi Aplikasi', $statistics['total_app_transactions']],
            ['Total Item Bank', $statistics['total_bank_items']],
            ['Total Matched', $statistics['matched_count']],
            ['Persentase Match', $statistics['match_percentage'].'%'],
            ['', ''],
            ['NOMINAL (IDR)', ''],
            ['Total Debit Aplikasi', number_format($statistics['total_app_debit'], 0, ',', '.')],
            ['Total Credit Aplikasi', number_format($statistics['total_app_credit'], 0, ',', '.')],
            ['Total Debit Bank', number_format($statistics['total_bank_debit'], 0, ',', '.')],
            ['Total Credit Bank', number_format($statistics['total_bank_credit'], 0, ',', '.')],
            ['', ''],
            ['Selisih Debit', number_format($statistics['total_app_debit'] - $statistics['total_bank_debit'], 0, ',', '.')],
            ['Selisih Credit', number_format($statistics['total_app_credit'] - $statistics['total_bank_credit'], 0, ',', '.')],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            6 => ['font' => ['bold' => true]],
            12 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = count($this->array()) + 1;
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:B'.$rowCount);
            },
        ];
    }
}

class ReconciliationMatchedSheet implements FromArray, WithColumnFormatting, WithEvents, WithHeadings, WithStyles, WithTitle
{
    protected $matched;

    public function __construct($matched)
    {
        $this->matched = $matched;
    }

    public function title(): string
    {
        return 'Transaksi Matched';
    }

    public function headings(): array
    {
        return [
            'Tanggal App',
            'Keterangan App',
            'Debit App',
            'Credit App',
            'Tanggal Bank',
            'Keterangan Bank',
            'Debit Bank',
            'Credit Bank',
            'Confidence',
            'Tipe Match',
        ];
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->matched as $match) {
            $appTransaction = $match['app_transaction'];
            $bankItem = $match['bank_item'];

            $data[] = [
                ($appTransaction->transaction_date instanceof Carbon)
                    ? $appTransaction->transaction_date->format('d/m/Y')
                    : (string) $appTransaction->transaction_date,
                $appTransaction->description,
                $appTransaction->debit_amount !== null ? (float) $appTransaction->debit_amount : null,
                $appTransaction->credit_amount !== null ? (float) $appTransaction->credit_amount : null,
                ($bankItem->date instanceof Carbon)
                    ? $bankItem->date->format('d/m/Y')
                    : (string) $bankItem->date,
                $bankItem->description,
                $bankItem->debit !== null ? (float) $bankItem->debit : null,
                $bankItem->credit !== null ? (float) $bankItem->credit : null,
                isset($match['confidence']) ? ((float) $match['confidence'] / 100) : null,
                isset($match['match_criteria']) ? implode(', ', $match['match_criteria']) : (isset($match['match_reasons']) ? implode(', ', $match['match_reasons']) : 'N/A'),
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = count($this->matched) + 1;
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:J'.$rowCount);
                $range = 'I2:I'.$rowCount;

                $green = new Conditional;
                $green->setConditionType(Conditional::CONDITION_CELLIS)
                    ->setOperatorType(Conditional::OPERATOR_GREATERTHANOREQUAL)
                    ->addCondition('0.9');
                $green->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('C6EFCE');
                $green->getStyle()->getFont()->getColor()->setARGB('006100');

                $yellow = new Conditional;
                $yellow->setConditionType(Conditional::CONDITION_CELLIS)
                    ->setOperatorType(Conditional::OPERATOR_BETWEEN)
                    ->addCondition('0.75')
                    ->addCondition('0.9');
                $yellow->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEB9C');
                $yellow->getStyle()->getFont()->getColor()->setARGB('9C6500');

                $red = new Conditional;
                $red->setConditionType(Conditional::CONDITION_CELLIS)
                    ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
                    ->addCondition('0.75');
                $red->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC7CE');
                $red->getStyle()->getFont()->getColor()->setARGB('9C0006');

                $sheet->getStyle($range)->setConditionalStyles([$green, $yellow, $red]);
            },
        ];
    }
}

class ReconciliationUnmatchedAppSheet implements FromArray, WithColumnFormatting, WithEvents, WithHeadings, WithStyles, WithTitle
{
    protected $unmatched;

    public function __construct($unmatched)
    {
        $this->unmatched = $unmatched;
    }

    public function title(): string
    {
        return 'App Unmatched';
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Keterangan',
            'Debit',
            'Credit',
            'Tipe Transaksi',
            'Source ID',
        ];
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->unmatched as $transaction) {
            $data[] = [
                ($transaction->transaction_date instanceof Carbon)
                    ? $transaction->transaction_date->format('d/m/Y')
                    : (string) $transaction->transaction_date,
                $transaction->description,
                $transaction->debit_amount !== null ? (float) $transaction->debit_amount : null,
                $transaction->credit_amount !== null ? (float) $transaction->credit_amount : null,
                $transaction->source_table,
                $transaction->source_id,
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = count($this->unmatched) + 1;
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:F'.$rowCount);
            },
        ];
    }
}

class ReconciliationUnmatchedBankSheet implements FromArray, WithColumnFormatting, WithEvents, WithHeadings, WithStyles, WithTitle
{
    protected $unmatched;

    public function __construct($unmatched)
    {
        $this->unmatched = $unmatched;
    }

    public function title(): string
    {
        return 'Bank Unmatched';
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Keterangan',
            'Debit',
            'Credit',
            'Bank Item ID',
        ];
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->unmatched as $item) {
            $data[] = [
                ($item->date instanceof Carbon)
                    ? $item->date->format('d/m/Y')
                    : (string) $item->date,
                $item->description,
                $item->debit !== null ? (float) $item->debit : null,
                $item->credit !== null ? (float) $item->credit : null,
                $item->id,
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = count($this->unmatched) + 1;
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:E'.$rowCount);
            },
        ];
    }
}
