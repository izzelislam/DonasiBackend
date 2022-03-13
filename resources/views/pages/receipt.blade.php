<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Donasi"
      page="kwitansi"
      link="donations.index"
    />
  </x-slot>

  <x-card 
    title="Kwitansi untuk {{ $donation->receipt_uid }}"
     :buttons="[
      'print'      => ['name' => 'print', 'url' => '/donations/print/receipt?uid='.$donation->receipt_uid],    
    ]"   
  >
    <div class="col mt-4">
      <hr>
      <table width="100%">
        <tr>
          <td>
            <img src="{{ Storage::url($setting->image ?? '') }}" alt="" style="width: 200px">
          </td>
          <td class="text-center">
            <h3>{{ $setting->name ?? '' }}</h3>
            <p style="margin: 0px; padding: 0px 10% 0px 10%; ">{{ $setting->address ?? '' }}</p>
            <p>{{ $setting->phone_number ?? '' }}</p>
          </td>
          <td>
            <img src="{{ asset($donation->donor->qr) }}" alt="" style="width: 150px;">
          </td>
        </tr>
      </table>
      <hr>
      <div style="padding-left: 10px;">
        <p><strong>No kwitansi: {{ $donation->receipt_uid }}</strong></p>
        <p>Telah di terima donasi dari :</p>
      </div>
      <hr>
      <table style="width: 100%;" cellpadding="10">
        <tr>
          <td>Nama Donatur</td>
          <td>{{ $donation->donor->name }}</td>
        </tr>
        <tr>
          <td>No Telepon</td>
          <td>{{ $donation->donor->phone_number }}</td>
        </tr>
        <tr>
          <td>Alamat</td>
          <td>{{ $donation->donor->address }}</td>
        </tr>
        <tr>
          <td>Jenis Donasi</td>
          <td>{{ $donation->type }}</td>
        </tr>
        <tr>
          <td>Jumlah Donasi</td>
          <td><b>Rp. {{number_format( $donation->amount )}}</b></td>
        </tr>
        <tr>
          <td>Penerima</td>
          <td>{{ $donation->recipient }}</td>
        </tr>
        <tr>
          <td>Tanggal Donasi</td>
          <td>{{ $donation->created_at->format('d/m/Y H:i:s') }}</td>
        </tr>
        <tr>
          <td>catatan</td>
          <td>{{ $donation->note }}</td>
        </tr>
      </table>
      <hr>
    </div>
  </x-card>
</x-layouts.app>