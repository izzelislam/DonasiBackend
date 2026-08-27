@php
    $donor = \App\Models\Donor::all();
    $donor_chunk = $donor->chunk(100)->toArray();
@endphp

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
      <div class="col-12 col-md-8">
        <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @isset($setting->image)
            <div class="mb-3">
              <img src="{{ asset($setting->image) }}" style="max-width: 250px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px;" alt="Logo Lembaga">
            </div>
          @endisset

          <x-form-input
            label="Foto / Logo Lembaga"
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
          />
          <x-form-input
            label="No Telepon / Hotline"
            name="phone_number"
            type="text"
            placeholder="isikan no telepon / hotline"
            value="{{ $setting->phone_number ?? '' }}"
          />
          <x-form-textarea
            label="Alamat Lembaga"
            name="address"
            placeholder="isikan alamat lembaga"
            value="{{ $setting->address ?? '' }}"
          />

          <div class="form-group mb-3">
            <label class="form-control-label font-weight-bold text-dark">
              Tulisan / Pesan Footer Tanda Terima
            </label>
            <textarea 
              name="receipt_footer" 
              class="form-control" 
              rows="3" 
              placeholder="Contoh: Terima kasih telah menyalurkan donasi melalui {{ $setting->name ?? 'mutiara hikmah official' }}. Semoga layanan kami mendatangkan manfaat bagi anda."
            >{{ $setting->receipt_footer ?? '' }}</textarea>
            <small class="form-text text-muted">
              *Opsional. Jika dikosongkan, akan otomatis menggunakan pesan default: <em>"Terima kasih telah menyalurkan donasi melalui {{ $setting->name ?? 'mutiara hikmah official' }}. Semoga layanan kami mendatangkan manfaat bagi anda."</em>
            </small>
          </div>

          <div class="mt-3">
            <button class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>

        <hr class="my-4">
        <h6 class="font-weight-bold">Print Qrcode</h6>
        <div class="mt-3">
          <div class="row">
            <div class="col">
              {{-- <a target="_blank" href="/donors/export/qr" class="btn btn-primary"> 
                <i class="fa fa-print"></i>
                <span class="ml-5">  Print Qr</span>
              </a> --}}
              @foreach ($donor_chunk as $item)
                <a target="_blank" href="/donors/export/qr?start={{ reset($item)['id'] }}&end={{ end($item)['id'] }}" class="btn btn-primary btn-sm me-1 mb-2"> 
                  <i class="fa fa-print me-1"></i>
                  <span>{{ reset($item)['id'] }} sampai {{ end($item)['id'] }} Qr</span>
                </a>
              @endforeach
            </div>
          </div>
        </div>

      </div>
    </div>

  </x-card>
  
</x-layouts.app>
