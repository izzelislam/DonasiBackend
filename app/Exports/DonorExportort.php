<?php

namespace App\Exports;

use App\Models\Donor;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class DonorExportort implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
            'Kode',
            'Nama Donatur',
            'No telepon',
            'Status',
            'Kecamatan',
            'Kabupaten',
            'Provinsi',
            'Alamat'
        ];
    }

    public function query()
    {
        $donors =     Donor::query();
        $donors->with(['regency', 'district', 'province']);

        if (isset(request()->district_id)){
            $donors->where('district_id', request()->district_id);
        }

        if (isset(request()->regency_id)){
            $donors->where('regency_id', request()->regency_id);
        }

        if (isset(request()->province_id)){
            $donors->where('province_id', request()->province_id);
        }
        
        if (isset(request()->team_id)){
            $donors->where('team_id', request()->team_id);
        }
        
        return $donors;
    }

    public function map($donors): array
    {
        return [
            $donors->uuid,
            $donors->name,
            $donors->phone_number,
            $donors->status,
            $donors->regency->name,
            $donors->district->name,
            $donors->province->name,
            $donors->address
        ];
    }
}
