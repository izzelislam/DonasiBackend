@php
$month = [
  '01' => 'Januari',
  '02' => 'Februari',
  '03' => 'Maret',
  '04' => 'April',
  '05' => 'Mei',
  '06' => 'Juni',
  '07' => 'Juli',
  '08' => 'Agustus',
  '09' => 'September',
  '10' => 'Oktober',
  '11' => 'November',
  '12' => 'Desember',
];
@endphp

<x-layouts.app>
  <x-slot:addonstyle>
    <x-table-style/>
  </x-slot>

  <x-slot:breadcrumb>
    <x-breadcrumb
      page="report keuangan"
      url="reports.financials"
      title="Report"
    />
  </x-slot>

  {{-- filter --}}
  <div class="card mb-4 px-4 py-2">
    <form action="{{ route('reports.financials') }}" method="GET">
      @csrf
      <div class="row">

        <div class="col-2">
          <div class="form-group">
            <label for="exampleFormControlSelect1">Pilih Bulan</label>
            <select class="form-control" id="exampleFormControlSelect1" name="month" onchange="this.form.submit()">
              <option value="">-- pilih bulan --</option>
              @foreach ($month as $key => $value)
                <option value="{{ $key }}" {{ request()->month == $key ? 'selected' : '' }} >{{ $value }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-2">
          <div class="form-group">
            <label for="exampleFormControlSelect1">Pilih Tahun</label>
            <select class="form-control" id="exampleFormControlSelect1" name="year" onchange="this.form.submit()">
              <option value="">-- pilih tahun --</option>  
              @foreach ($years as $year )
                <option value="{{ $year->year }}" {{ request()->year == $year->year ? 'selected' : '' }} >{{ $year ->year}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-2">
          <div class="form-group">
            <label for="example-date-input" class="form-control-label">Pilih Tanggal dari</label>
            <input name="start_date" class="form-control" type="date" value="{{ request()->start_date ?? '' }}"  onchange="this.form.submit()" id="example-date-input">
          </div>
        </div>

        <div class="col-2">
          <div class="form-group">
            <label for="example-date-input" class="form-control-label">Pilih Tanggal Hingga</label>
            <input name="end_date" class="form-control" type="date" value="{{ request()->end_date ?? '' }}"  onchange="this.form.submit()" id="example-date-input">
          </div>
        </div>
  
        <div class="col-2 pt-4">
          <div class="flex">
            {{-- <button class="btn btn-success">Terapkan</button> --}}
            <a href="{{ route('reports.financials') }}" class="btn btn-primary">Reset</a>
          </div>
        </div>
      </div>
    </form>
  </div>


  {{-- rekap --}}
  <x-card class="px-4">
    <div class="d-flex justify-content-between">
      <div class="col-12">
        <h5>Rekap Keuangan {{ isset(request()->month) ? $month[request()->month] : $month[date('m')] }} tahun {{ isset(request()->year) ? request()->year : date('Y') }}</h5>
        <div class="row">
          <div class="col">
           Total Debet 
          </div>
          <div class="col">
            <b>Rp. {{ number_format($total_income_month) }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col">
            Total Kredit </i>
          </div>
          <div class="col">
            <b>Rp. {{ number_format($total_expense_month) }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-8"><hr></div>
          <div class="col-4">- <small>di kurang</small></div>
        </div>
        <div class="row">
          <div class="col">
            <b>Total saldo</b>
          </div>
          <div class="col">
            <b>Rp. {{ number_format($total_balance_month) }}</b>
          </div>
        </div>
      </div>
    </div>
    {{-- <hr> --}}
  </x-card>


  <x-card 
    title="Laporan Keuangan bulan {{ isset(request()->month) ? $month[request()->month] : $month[date('m')] }} tahun {{ isset(request()->year) ? request()->year : date('Y') }}" 
    :buttons="[
      'export-data'      => ['name' => 'export', 'url' => '/reports-financials/export/excel?'.request()->getQueryString()],    
    ]" 
  >

  <div class="table-responsive p-0">
    <table id="myTable" class="table" style="width:100%">
      <thead>
        <tr>
          <th>No</th>
          <th>Title</th>
          <th>Jumlah</th>
          <th>Jenis Transaksi</th>
          <th>Tanggal Transaksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($transactions as $index => $item)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->title ?? "" }}</td>
            <td >Rp. {{ number_format($item->amount) ?? "" }}</td>
            <td class="text-{{ $item->type == 'income' ? 'success'  : 'danger'}}">{{ $item->type == 'income' ? 'Masuk'  : 'Keluar'}} <i class="fa fa-arrow-{{ $item->type == 'income'? 'down'  : 'up'}}"></i></td>
            <td >{{ $item->created_at->format('d/m/Y H:i:s') ?? "" }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  </x-card>
  
  

  <x-slot:addonscript>
    <x-table-script/>
  </x-slot>

</x-layouts.app>