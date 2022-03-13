@php
    $donation = \App\Models\Donation::with('donor')->where('receipt_uid', request()->uid)->first();
    $setting = \App\Models\Setting::first();
@endphp
<div style="width: 1050px; border: 1px solid black;">
  <table width="100%">
    <tr>
      <td>
        <img src="{{ storage_path('app/public/'.$setting->image ?? '') }}" alt="" style="width: 200px">
      </td>
      <td class="text-center">
        <h3>{{ $setting->name ?? '' }}</h3>
        <span style="margin: 0px; padding: 0px;">{{ $setting->address ?? '' }}</span>
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
      <td style="width: 100px;">Nama Donatur</td>
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
      <td><b>Rp.{{ number_format($donation->amount) }}</b></td>
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
</div>