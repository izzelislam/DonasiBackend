<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Team"
      page="Tambah"
      link="fundrisers.index"
    />
  </x-slot>

  <x-card title="Tambah data fundriser">
    <form class="col-8" method="POST" action="{{ $action }}" enctype="multipart/form-data">
      @csrf
      @isset($model)
        @method('PUT')
      @endisset
      <x-form-select
        name="teams_id"
        label="Pilih Tim"
        :options="$teams"
        value="{{ $model->team_id ?? '' }}"
      />

      <x-form-input
        label="Nama"
        name="name"
        placeholder="isikan nama fundriser"
        value="{{ $model->name ?? '' }}"
      />

      <x-form-input
        label="Foto"
        name="photo"
        type="file"
        placeholder="isikan foto fundriser"
      />

      <x-form-input
        label="No Telepon"
        name="phone_number"
        placeholder="isikan No telepon fundriser"
        value="{{ $model->phone_number ?? '' }}"
      />

      <x-form-input
        label="Email"
        name="email"
        type="email"
        placeholder="isikan No telepon fundriser"
        value="{{ $model->email ?? '' }}"
      />

      <x-form-input
        label="Password Bawaan"
        name="password"
        type="password"
        placeholder="isikan password bawaan"
      />

      <button class="btn btn-primary">{{ isset($model) ? 'Update' : 'Tambah' }} Data</button>
  </form>
  
  </x-card>
</x-layouts.app>