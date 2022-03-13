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
      page="index"
      url="donations.index"
      title="Donasi"
    />
  </x-slot>

  {{-- filter --}}
  <div class="card mb-4 px-4 py-2">
    <form method="GET" action="{{ route('donations.index') }}">
      <div class="row">
        @csrf
        <div class="col-2">
          <div class="form-group">
            <label for="exampleFormControlSelect1">Pilih Tim</label>
            <select class="form-control" id="exampleFormControlSelect1" name="team_id" onchange="this.form.submit()">
              <option value="">-- pilih tim --</option>
              @foreach ($teams as $team)
                <option value="{{ $team->id }}" {{ request()->team_id == $team->id ? 'selected' : '' }} >{{ $team->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
  
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
            <a href="{{ route('donations.index') }}" class="btn btn-primary">Reset</a>
          </div>
        </div>
      </div>
    </form>
  </div>
  <x-card 
    title="Data donasi bulan {{ isset(request()->month) ? $month[request()->month] : $month[date('m')] }} tahun {{ isset(request()->year) ? request()->year : date('Y') }}" 
    :buttons="[
      'create'      => ['name' => 'create', 'url' => 'donations.create'],    
    ]" 
  >

  <div class="table-responsive p-0">
    <table id="myTable" class="table" style="width:100%">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Invoice</th>
          <th>Kabupaten</th>
          <th>Nominal</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($donations as $index => $item )
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->donor->name }}</td>
            <td><a href="{{ route('donations.receipt', $item->receipt_uid) }}">{{ $item->receipt_uid }}</a></td>
            <td>{{ $item->donor->regency->name }}</td>
            <td><b>Rp. {{ number_format($item->amount) }}</b></td>
            <td>
              <a href="{{ route('donations.show', $item->id) }}" class="btn btn-icon btn-3 btn-info" >
                <span class="btn-inner--icon"><i class="fas fa-eye"></i></span>
                <span class="btn-inner--text">detail</span>
              </a>
              <a href="{{ route('donations.edit', $item->id) }}" class="btn btn-icon btn-3 btn-warning" >
                <span class="btn-inner--icon"><i class="fas fa-edit"></i></span>
                <span class="btn-inner--text">edit</span>
              </a>
              <form action="{{ route('donations.destroy', $item->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-icon btn-3 btn-danger" >
                  <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                  <span class="btn-inner--text">delete</span>
                </button>
              </form>
            </td>
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