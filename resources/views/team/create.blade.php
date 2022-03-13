<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Team"
      page="Tambah"
      link="teams.index"
    />
  </x-slot>

  <x-card title="Tambah data tim">
    <form class="col-8" method="POST" action="{{ $action }}">
      @csrf
      @isset($model)
        @method('PUT')
      @endisset
      <x-form-input
        label="Nama Tim"
        name="name"
        placeholder="isikan nama Tim"
        value="{{ $model->name ?? '' }}"
      />

      <button class="btn btn-primary"> {{ isset($model->name) ? 'Update' : 'Tambah' }} Data</button>
  </form>
  
  </x-card>
</x-layouts.app>