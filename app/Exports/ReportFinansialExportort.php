<?php

namespace App\Exports;

use App\Models\Finance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportFinansialExportort implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 16]],
        ];
    }

    public function headings(): array
    {
        return [
            [
                'Kode Transaksi',
                'Nama Transaksi',
                'Tanggal',
                'Debet',
                'Kredit',
                'Keterangan'
            ]
        ];
    }

    public function query()
    {
        $financials = Finance::query();

        

        if (isset(request()->start_date)){
            $financials->where('created_at', '>=', request()->start_date);
        }

        if (isset(request()->end_date)){
            $financials->where('created_at', '<=', request()->end_date);
        }

        if (isset(request()->start_date) && isset(request()->end_date)){
            $financials->whereBetween('created_at', [request()->start_date, request()->end_date]);
        }

        if (isset(request()->month)){
            $financials->whereMonth('created_at', request()->month);
        }else{
            $financials->whereMonth('created_at', date('m'));
        }

        if (isset(request()->year)){
            $financials->whereYear('created_at', request()->year);
        }else{
            $financials->whereYear('created_at', date('Y'));
        }

        return $financials;
    }

    public function map($financials): array
    {
        return [
            $financials->receipt_uid,
            $financials->title,
            $financials->created_at->format('d/m/Y H:i:s'),
            $financials->type == 'income' ? $financials->amount : '',
            $financials->type == 'expense' ? $financials->amount : '',
            
            // $financials->type == 'income' ? 'Pemasukan' : 'Pengeluaran',
            $financials->note
        ];
    }
}
