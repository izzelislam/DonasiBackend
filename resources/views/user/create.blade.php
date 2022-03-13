<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="User"
      page="Tambah"
      link="users.index"
    />
  </x-slot>

  <x-card title="Tambah data fundriser">
    <form class="col-8" method="POST" action="{{$action}}">
      @csrf
      @isset($model)
          @method('PUT')
      @endisset
      <x-form-input
        label="Nama"
        name="name"
        value="{{ $model->name ?? '' }}"
        placeholder="isikan nama user"
      />

      <x-form-input
        label="No Telepon"
        name="phone_number"
        value="{{ $model->phone_number ?? '' }}"
        placeholder="isikan No telepon user"
      />

      <x-form-input
        label="Email"
        name="email"
        type="email"
        value="{{ $model->email ?? '' }}"
        placeholder="isikan No telepon user"
      />

      <x-form-input
        label="Password Bawaan"
        name="password"
        type="password"
        placeholder="isikan password bawaan"
      />

      <button class="btn btn-primary">
        @if (isset($model))
          Ubah
        @else
          Tambah
        @endif
        Data
      </button>
  </form>
  
  </x-card>
</x-layouts.app>