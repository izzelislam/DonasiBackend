
<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Donasi"
      page="Detail"
      link="donations.index"
    />
  </x-slot>

  <x-card title="Detail data donasi">
    <div class="row">
      <div class="col-6">
        <table class="table">
          <tr>
            <td>Nama</td>
            <td>{{ $donation->donor->name }}</td>
          </tr>

          <tr>
            <td>No. HP</td>
            <td>{{ $donation->donor->phone_number }}</td>
          </tr>

          <tr>
            <td>Kecamatan</td>
            <td>{{ $donation->donor->district->name }}</td>
          </tr>

          <tr>
            <td>Kabupaten</td>
            <td>{{ $donation->donor->regency->name }}</td>
          </tr>

          <tr>
            <td>Provinsi</td>
            <td>{{ $donation->donor->province->name }}</td>
          </tr>

          <tr>
            <td>Alamat</td>
            <td>{{ $donation->donor->address }}</td>
          </tr>
        </table>
      </div>
      <div class="col-6">
        <table class="table">
          <tr>
            <td>Nama Penerima</td>
            <td>{{ $donation->recipient }}</td>
          </tr>

          <tr>
            <td>Jenis Donasi</td>
            <td>{{ $donation->type }}</td>
          </tr>

          <tr>
            <td>Nominal</td>
            <td><b>Rp {{ number_format($donation->amount) }}</b></td>
          </tr>

          <tr>
            <td>Tanggal Diterima</td>
            <td>{{ $donation->created_at->format('d/m/Y H:i:s') }}</td>
          </tr>

          <tr>
            <td>Invoice</td>
            <td>{{ $donation->receipt_uid }} <small><i><a href="/donations/print/receipt?uid={{ $donation->receipt_uid }}">download invoice</a></i></small></td>
          </tr>
        </table>
      </div>
    </div>
  </x-card>

</x-layouts.app>