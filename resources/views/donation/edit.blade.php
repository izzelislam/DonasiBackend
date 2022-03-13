@php
    $donor_data = isset($donor) ? $donor : 'hide';
@endphp
<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Donasi"
      page="Edit"
      link="donations.index"
    />
  </x-slot>



  <x-card title="Tambah data danasi">
    <div x-data="Donation()">
      <form class="col-8" method="POST" action="{{ $action }}">
        @csrf
        @method('PUT')
        <x-form-input
          label="Nama Donatur"
          name="name"
          placeholder="isikan nama donatur"
          class="donor_name"
          value="{{ $donation->donor->name ?? '' }}"
          readonly="readonly"
        />

        <x-form-input
          label="Kode Donatur"
          name="uuid"
          placeholder="kode donatur"
          class="donor_uuid"
          value="{{ $donation->donor->uuid ?? '' }}"
          readonly="readonly"
        />

        <template x-if="!ischecked">
          <x-form-input
            label="Penerima"
            name=""
            placeholder="kode donatur"
            class=""
            value="{{ $donation->recipient ?? '' }}"
            readonly="readonly"
          />
        </template>

        <template x-if="ischecked">
          <x-form-select
            label="Penerima"
            name="recipient_id"
            :options="$recipients ?? []"
          />
       </template>

        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" id="fcustomCheck1" x-on:change="Change()">
          <label class="custom-control-label" for="customCheck1">Ubah penerima donasi</label>
        </div>
        

        <x-form-select
          label="Jenis Donasi"
          name="type"
          :default=" ['value' => $donation->type ?? '', 'label' => $donation->type?? '']"
          :options="['zakat' => 'zakat', 'infaq' => 'infaq', 'shodaqoh' => 'shodaqoh', 'wakaf' => 'wakaf']"
        />

        <x-form-input
          label="Jumlah Donasi"
          name="amount"
          type="number"
          value="{{ $donation->amount ?? '' }}"
          placeholder="isikan jumlah donasi / kosongkan jika donasi berbentuk barang"
        />
        
        <x-form-textarea
          label="Catatan"
          name="note"
          placeholder="isikan catatan"
          value="{{ $donation->note ?? '' }}"
        />

        <button class="btn btn-primary">Update Data</button>
      </form>
    </div>
  </x-card>

  <x-slot:addonscript>
    <script defer src="https://unpkg.com/alpinejs@3.9.1/dist/cdn.min.js"></script>

    <script>
      function Donation() {
        const donors =  @json($donor_data) ;
        return {
          // data
          ischecked: false,
          // methods
          Change(){
            var recipent = document.getElementById("fcustomCheck1");
            this.ischecked = recipent.checked
          },
          init () {
            var recipent = document.getElementById("fcustomCheck1");
            recipent.checked = false
          },
        }
      }
    </script>
  </x-slot>

</x-layouts.app>