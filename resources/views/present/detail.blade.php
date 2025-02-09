
<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Presensi"
      page="Detail"
      link="presents.index"
    />
  </x-slot>

  <x-card title="Detail data presensi">
    <div class="row">
      <div class="col-3">
        <img src="{{ asset($model->user->image) }}" class="w-100" alt="">
      </div> 
      <div class="col-4" class="table-resposive">
        <table class="table">
          <tr>
            <td>Nama</td>
            <td>{{ $model->user->name }}</td>
          </tr>
          <tr>
            <td>No Hp</td>
            <td>{{ $model->user->phone_number }}</td>
          </tr>
          <tr>
            <td>Email</td>
            <td>{{ $model->user->email }}</td>
          </tr>
          <tr>
            <td>Catatan</td>
            <td>{{ $model->note }}</td>
          </tr>

        </table>
      </div>
      <div class="col-4 table-responsive">
        <table class="table align-middle mb-4">
          <tr>
            <td>Tipe Presensi</td>
            <td>{{ $model->type }}</td>
          </tr>
          <tr style="width: 20px;">
            <td>Waktu Presensi</td>
            <td>{{ $model->present_at }}</td>
          </tr>
          <tr>
            <td>latitude</td>
            <td>{{ $model->lat }}</td>
          </tr>
          <tr>
            <td>longitude</td>
            <td>{{ $model->long }}</td>
          </tr>

        </table>
        {{--  link to map with lat long --}}
        <div>
          <b><small>Lihat di map :</small></b>
          <a href="https://www.google.com/maps/search/{{ $model->lat }},{{ $model->long }}" target="_blank" class="btn btn-primary">Lihat di Maps</a>
        </div>
      </div>
    </div>
  </x-card>

</x-layouts.app>