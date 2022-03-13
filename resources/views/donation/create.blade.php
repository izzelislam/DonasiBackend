@php
    $donor_data = isset($donor) ? $donor : 'hide';
@endphp
<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Donasi"
      page="Tambah"
      link="donations.index"
    />
  </x-slot>



  <x-card title="Tambah data danasi">
    <div x-data="Donation()">
      <template x-if="!isDonor">
        <form action="{{ route('donors.search.person') }}" method="POST">
          @csrf
          <x-form-input
            label="Kode Donatur"
            name="uuid"
            placeholder="isikan kode donatur"
          />
          
          @if(session('error'))
            <div class="mt-0 mb-2">
              <i class="text-danger"><small><b>{{ session('error') }}</b></small></i>
            </div>
          @endif

          <button class="btn btn-primary">lanjut</button>
        </form>
      </template>
      <template x-if="isDonor">
        <form class="col-8" method="POST" action="{{ route('donations.store') }}">
          @csrf
          <x-form-input
            label="Nama Donatur"
            name="name"
            placeholder="isikan nama donatur"
            class="donor_name"
            value="{{ $donor_data->name ?? '' }}"
            readonly="readonly"
          />
  
          <x-form-input
            label="Kode Donatur"
            name="uuid"
            placeholder="kode donatur"
            class="donor_uuid"
            value="{{ $donor_data->uuid ?? '' }}"
            readonly="readonly"
          />

          <x-form-select
            label="Penerima"
            name="recipient_id"
            :options="$recipients ?? []"
          />
  

          <x-form-select
            label="Jenis Donasi"
            name="type"
            :options="['zakat' => 'zakat', 'infaq' => 'infaq', 'shodaqoh' => 'shodaqoh', 'wakaf' => 'wakaf']"
          />
  
          <x-form-input
            label="Jumlah Donasi"
            name="amount"
            type="number"
            placeholder="isikan jumlah donasi / kosongkan jika donasi berbentuk barang"
          />
          
          <x-form-textarea
            label="Catatan"
            name="note"
            placeholder="isikan catatan"
          />
  
          <button class="btn btn-primary">Tambah Data</button>
        </form>
      </template>
    </div>
  </x-card>

  <x-slot:addonscript>
    <script defer src="https://unpkg.com/alpinejs@3.9.1/dist/cdn.min.js"></script>

    <script>
      function Donation() {
        const donors =  @json($donor_data) ;
        return {
          // data
          isDonor: false,
          // methods
          init () {
            if (donors != 'hide') {
              this.isDonor = true;
            }
            if (this.isDonor){
              
            }
          },
        }
      }
    </script>
  </x-slot>

</x-layouts.app>