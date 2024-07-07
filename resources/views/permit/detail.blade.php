
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
            <td>Tanggal Perizinan</td>
            <td>{{ date("d/m/Y", strtotime($model->permit_at)) }}</td>
          </tr>
          <tr>
            <td>Catatan</td>
            <td>{{ $model->note }}</td>
          </tr>

        </table>
      </div>
    </div>
  </x-card>

</x-layouts.app>