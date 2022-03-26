<x-layouts.app>
  <x-slot:addonstyle>
    <x-table-style/>
  </x-slot>

  <x-slot:breadcrumb>
    <x-breadcrumb
      page="index"
      url="settings.index"
      title="Setting"
    />
  </x-slot>

  <x-card 
    title="Halaman Setting" 
  >

    <div class="row">
      <div class="col-8">
        <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @isset($setting->image)
            <div>
              <img src="{{ asset($setting->image) }}" style="width: 250px;" alt="">
            </div>
          @endisset

          <x-form-input
            label="Foto"
            name="photo"
            type="file"
            placeholder="isikan foto lembaga"
          />
          <x-form-input
            label="Nama Lembaga"
            name="name"
            type="text"
            placeholder="isikan nama lembaga"
            value="{{ $setting->name ?? '' }}"
            uppercase={{ true }}
          />
          <x-form-input
            label="No Telepon"
            name="phone_number"
            type="text"
            placeholder="isikan nama no telepon"
            value="{{ $setting->phone_number ?? '' }}"
          />
          <x-form-textarea
            label="Alamat"
            name="address"
            placeholder="isikan alamat donatur"
            value="{{ $setting->address ?? '' }}"
          />

          <div>
            <button class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>

  </x-card>
  
 

</x-layouts.app>