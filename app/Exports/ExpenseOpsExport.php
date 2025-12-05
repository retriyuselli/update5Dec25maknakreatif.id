<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpenseOpsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(public Collection $records)
    {
        //
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->records;
    }

    public function map($expenseops): array
    {
        return [
            $expenseops->name,
            $expenseops->amount,
            $expenseops->date_expense,
            $expenseops->no_nd,
            $expenseops->note,
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'amount',
            'date_expense',
            'no_nd',
            'note',
        ];
    }
}
