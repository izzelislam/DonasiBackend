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
      page="report donasi"
      url="reports.donations"
      title="Report"
    />
  </x-slot>

  @include('report.filter')
  
  <x-card 
    title="Laporan donasi  bulan {{ isset(request()->month) ? $month[request()->month] : $month[date('m')] }} tahun {{ isset(request()->year) ? request()->year : date('Y') }}" 
    :buttons="[
      'export-data'      => ['name' => 'export', 'url' => '/reports-donations/export/excel?'.request()->getQueryString()],    
    ]" 
  >

  <div class="table-responsive p-0">
    <table id="myTable" class="table"  style="width:100%">
      <thead>
        <tr>
          <th>No</th>
          <th style="width:200px;">Nama</th>
          <th>Tanggal</th>
          <th>Jumlah</th>
          <th>Kabupaten</th>
          <th>Provinsi</th>
          <th style="width:20%;" >Alamat</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($donations as $index => $donation) 
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $donation->donor->name }}</td>
            <td>{{ $donation->created_at->format('d/m/Y H:i:s') }}</td>
            <td>Rp. {{ number_format($donation->amount) }}</td>
            <td>{{ $donation->donor->regency->name }}</td>
            <td>{{ $donation->donor->province->name }}</td>
            <td>{{ $donation->donor->address }}</td>
         
          </tr>
        @endforeach
      </tbody>
    </table>
    <hr>
    <div>
       <b class="float-end">TOTAL DONASI : Rp. {{ number_format($total_donation) }}</b>
    </div>
  </div>
    
  </x-card>
  
  <x-slot:addonscript>
    <x-table-script/>
    <script>
      $(document).ready(function() {
        $('#myTable').DataTable({
          scrollX:false,
          fixedColumns: true,
        });
      });
    </script>
  </x-slot>

</x-layouts.app>