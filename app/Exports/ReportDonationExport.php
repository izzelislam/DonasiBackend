<?php

namespace App\Exports;

use App\Models\Donation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class ReportDonationExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Donatur',
            'Tanggal',
            'Jumlah',
            'Kecamatan',
            'Kabupaten',
            'Provinsi',
            'Alamat'
        ];
    }

    public function query()
    {
      $donation =     Donation::query();
        $donation->select('donor_id', 'created_at', 'amount', 'recipient', 'note')
        ->with(['donor.regency', 'donor.district', 'donor.province']);

        if (isset(request()->team_id)){
            $donation->whereHas('donor', function ($query) {
                $query->where('team_id', request()->team_id);
            });
        }

        if (isset(request()->start_date)){
            $donation->where('created_at', '>=', request()->start_date);
        }

        if (isset(request()->end_date)){
            $donation->where('created_at', '<=', request()->end_date);
        }

        if (isset(request()->start_date) && isset(request()->end_date)){
            $donation->whereBetween('created_at', [request()->start_date, request()->end_date]);
        }

        if (isset(request()->month)){
            $donation->whereMonth('created_at', request()->month);
        }else{
            $donation->whereMonth('created_at', date('m'));
        }

        if (isset(request()->year)){
            $donation->whereYear('created_at', request()->year);
        }else{
            $donation->whereYear('created_at', date('Y'));
        }
      return $donation;
    }

    public function map($donation): array
    {
        return [
            $donation->donor->name ?? "",
            $donation->created_at->format('d/m/Y' ?? ""),
            $donation->amount ?? "",
            $donation->donor->district->name ?? "",
            $donation->donor->regency->name ?? "",
            $donation->donor->province->name ?? "",
            $donation->donor->address ?? ""
        ];
    }
}