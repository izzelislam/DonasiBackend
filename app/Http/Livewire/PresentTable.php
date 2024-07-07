<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Present;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Symfony\Component\Finder\Iterator\DateRangeFilterIterator;

class PresentTable extends DataTableComponent
{
    protected $model = Present::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function filters(): array
    {
        
        return [
            DateFilter::make('Waktu Presensi')
                ->filter(function (Builder $builder, $val) {
                    $builder ->whereDate('presents.present_at', $val);
                })
            ,
            // SelectFilter::make('Status')
            // ->options([
            //     'all' => 'Semua',
            //     1 => 'Aktif',
            //     0 => 'Tidak Aktif',

            // ])->filter(function (Builder $builder, $val) {
            //     if($val != 'all'){
            //         $builder
            //             ->where('employs.status', $val); // minDate is the start date selected
            //     }
            // }),
            // SelectFilter::make('Jabatan')
            // ->options($jabatan)->filter(function (Builder $builder, $val) {
            //     if($val != 'all'){
            //         $builder
            //             ->where('employs.position_id', $val); // minDate is the start date selected
            //     }
            // }),
            SelectFilter::make('Status')
            ->options([
                'all' => 'Semua',
                "masuk" => "Masuk",
                "keluar" => "Keluar",
                "mulai_istirahat" => "Mulai Istirahat",
                "selesai_istirahat" => "Selesai Istirahat",
                "mulai_lembur" => "Mulai Lembur",
                "selesai_lembur" => "Selesai Lembur",
            ])->filter(function (Builder $builder, $val) {
                if($val != 'all'){
                    $builder
                        ->where('presents.type', $val); // minDate is the start date selected
                }
            }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->hideIf(true)
                ->sortable(),
            Column::make("User id", "user_id")
                ->hideIf(true)
                ->sortable(),
            Column::make("Fundraiser")
                ->label(function ($row) {
                    return '
                        <div class="d-flex">
                            <div class="me-3">
                                <div style="
                                        height: 60px;
                                        width: 60px;
                                        border-radius: 10px;
                                        background-size: cover;
                                        background-position: center;
                                        background-image: url('.asset($row->user->image).');
                                    ">
                                </div>
                            </div>
                            <div >
                                <b>'.$row->user->name.'</b><br>
                                <small>'.$row->user->email.'</small><br>
                            </div>
                        </div>
                    ';
                })
                ->html()
                ->sortable(),
            Column::make("Waktu Presensi", "present_at")
                ->format(fn ($value) => date('l d-M-Y H:i', strtotime($value)))
                ->sortable(),
            Column::make("Jenis Presensi", "type")
                ->searchable()
                ->sortable(),
            Column::make("Catatan", "note")
                ->searchable()
                ->sortable(),
            Column::make("Lat", "lat")
                ->hideIf(true)
                ->sortable(),
            Column::make("Long", "long")
                ->hideIf(true)
                ->sortable(),
            Column::make("Created at", "created_at")
                ->hideIf(true)
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->hideIf(true)
                ->sortable(),
            Column::make('Aksi')
                ->unclickable()
                ->label(
                    fn ($row, Column $column) => view('components.action-button')->with(
                        [
                            'detail'   => route("presents.show", $row->id),
                            // 'edit'   => route("admin.vocabulary.edit", $row->id),
                            'delete' => route("presents.destroy", $row->id),
                        ]
                    )
                )->html(),
        ];
    }
}
