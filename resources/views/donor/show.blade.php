
<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Donatur"
      page="Detail"
      link="donors.index"
    />
  </x-slot>

  <x-card title="Detail data donatur">
    <div class="row">
      <div class="col-3">
        <div id="qr"></div>
        {{-- <img src="{{ asset($model->qr) }}" class="w-100" alt=""> --}}
      </div> 
      <div class="col-4" class="table-resposive">
        <table class="table">
          <tr>
            <td>Nama</td>
            <td>{{ $model->name }}</td>
          </tr>
          <tr>
            <td>Penanggung jawab</td>
            <td>{{ $model->team->name }}</td>
          </tr>
          <tr>
            <td>Status</td>
            <td><span class="badge badge-pill bg-{{ $model->status == 'active' ? 'success' : 'danger' }}">{{ $model->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}</span></td>
          </tr>
          <tr>
            <td>No Hp</td>
            <td>{{ $model->phone_number }}</td>
          </tr>
          <tr>
            <td>Kecematan</td>
            <td>{{ $model->district->name }}</td>
          </tr>
          <tr>
            <td>Kabupaten</td>
            <td>{{ $model->regency->name }}</td>
          </tr>
          <tr>
            <td>Provinsi</td>
            <td>{{ $model->province->name }}</td>
          </tr>
        </table>
      </div>
      <div class="col-4 table-responsive">
        <table class="table align-middle">
          <tr>
            <td>Kode</td>
            <td id="code">{{ $model->uuid }}</td>
          </tr>
          <tr style="width: 20px;">
            <td>Alamat</td>
            <td>{{ $model->address }}</td>
          </tr>
          <tr>
            <td>latitude</td>
            <td>{{ $model->lat }}</td>
          </tr>
          <tr>
            <td>longitude</td>
            <td>{{ $model->lng }}</td>
          </tr>
          <tr>
            <td>Total Donasi</td>
            <td><b>Rp {{ number_format($model->donations_sum_amount) }}</b></td>
          </tr>
        </table>
      </div>
    </div>
  </x-card>

  <x-slot:addonscript>
    <script src=
"https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js">
    </script>

    <script>
      var code = document.getElementById("code");
      console.log(code.innerText)
      var qrcode = new QRCode("qr", code.innerText);
    </script>
  </x-slot:addonscript>

</x-layouts.app>