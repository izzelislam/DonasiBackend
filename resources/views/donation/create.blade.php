@php
    $donor_data = isset($donor) ? $donor : null;
    $bankAccountList = \App\Enums\BankAccount::toArray();
    $currentCreatedAt = old('created_at', now()->format('Y-m-d\TH:i'));
@endphp
<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Donasi"
      page="Tambah"
      link="donations.index"
    />
  </x-slot>

  <style>
    .bank-info-card {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-left: 4px solid #00A859;
      border-radius: 8px;
      padding: 10px 14px;
      margin-top: 8px;
    }
    .donor-search-wrapper {
      position: relative;
    }
    .donor-results-dropdown {
      position: absolute;
      top: calc(100% + 4px);
      left: 0;
      right: 0;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      max-height: 280px;
      overflow-y: auto;
      z-index: 1050;
    }
    .donor-item {
      padding: 10px 14px;
      border-bottom: 1px solid #f1f5f9;
      cursor: pointer;
    }
    .donor-item:hover {
      background: #f0fdf4;
    }
  </style>

  <x-card title="Tambah data donasi">
    <div x-data="Donation()">
      
      {{-- STEP 1: SEARCH DONATUR (MINIMALIS) --}}
      <div x-show="!isDonor" style="{{ $donor_data ? 'display: none;' : '' }}">
        <div class="donor-search-wrapper col-12 col-md-8">
          <div class="form-group mb-2">
            <label class="form-control-label">Cari Donatur (Nama / No. HP / Kode)</label>
            <input 
              type="text" 
              class="form-control" 
              placeholder="Ketik nama atau kode donatur..." 
              x-model="searchQuery"
              @input.debounce.250ms="searchDonors()"
              @focus="if(results.length > 0) showDropdown = true"
              autocomplete="off"
            >
          </div>

          {{-- Dropdown Hasil Pencarian --}}
          <div class="donor-results-dropdown" x-show="showDropdown" @click.outside="showDropdown = false" style="display: none;">
            <template x-for="item in results" :key="item.id">
              <div class="donor-item" @click="selectDonor(item)">
                <div class="d-flex justify-content-between align-items-center">
                  <strong class="text-dark text-sm" x-text="item.name"></strong>
                  <span class="badge bg-gradient-secondary text-xxs font-monospace" x-text="item.uuid"></span>
                </div>
                <div class="text-xs text-muted mt-1">
                  <span x-show="item.phone_number" x-text="item.phone_number + ' • '"></span>
                  <span x-text="item.location || item.address || ''"></span>
                </div>
              </div>
            </template>
            <div class="p-3 text-muted text-center text-xs" x-show="results.length === 0 && searchQuery.length > 0 && !loading">
              Donatur tidak ditemukan.
            </div>
            <div class="p-3 text-muted text-center text-xs" x-show="loading">
              Mencari...
            </div>
          </div>
        </div>

        <div class="col-12 col-md-8 mt-2">
          <small class="text-muted">Atau masukkan kode donatur secara manual:</small>
          <form action="{{ route('donors.search.person') }}" method="POST" class="mt-2">
            @csrf
            <div class="input-group">
              <input 
                type="text" 
                name="uuid" 
                class="form-control @error('uuid') is-invalid @enderror" 
                placeholder="Kode Donatur"
              >
              <button class="btn btn-primary mb-0">Lanjut</button>
            </div>
            @error('uuid')
              <div class="text-danger mt-1"><small><b>{{ $message }}</b></small></div>
            @enderror
            @if(session('error'))
              <div class="text-danger mt-1"><small><b>{{ session('error') }}</b></small></div>
            @endif
          </form>
        </div>
      </div>

      {{-- STEP 2: FORM INPUT DONASI --}}
      <div x-show="isDonor" style="{{ $donor_data ? '' : 'display: none;' }}">
        <form class="col-12 col-md-8" method="POST" action="{{ route('donations.store') }}" enctype="multipart/form-data">
          @csrf
          
          <div class="form-group mb-3">
            <label class="form-control-label">Nama Donatur</label>
            <input 
              type="text" 
              name="name" 
              class="form-control donor_name" 
              placeholder="Nama donatur" 
              x-model="donorName" 
              :value="donorName" 
              readonly
            >
          </div>
  
          <div class="form-group mb-3">
            <label class="form-control-label">Kode Donatur</label>
            <input 
              type="text" 
              name="uuid" 
              class="form-control donor_uuid" 
              placeholder="Kode donatur" 
              x-model="donorUuid" 
              :value="donorUuid" 
              readonly
            >
          </div>

          <div class="form-group mb-3">
            <label class="form-control-label text-dark font-weight-bold" for="created_at">
              Tanggal & Waktu Transaksi
            </label>
            <input 
              type="datetime-local" 
              name="created_at" 
              id="created_at" 
              class="form-control @error('created_at') is-invalid @enderror" 
              value="{{ $currentCreatedAt }}"
            >
          </div>

          <x-form-select
            label="Penerima"
            name="recipient_id"
            :options="$recipients ?? []"
            :value="old('recipient_id', auth()->id())"
          />

          <x-form-select
            label="Jenis Donasi"
            name="type"
            :options="['zakat' => 'zakat', 'infaq' => 'infaq', 'shodaqoh' => 'shodaqoh', 'wakaf' => 'wakaf']"
            :value="old('type', 'infaq')"
          />

          <!-- Rekening Bank Penerima -->
          <div class="form-group mb-3">
            <label class="form-control-label text-dark font-weight-bold">
              Rekening Bank Penerima
            </label>
            <select name="bank_account" id="select-bank-account" class="form-control" onchange="updateBankDisplay()">
              <option value="">-- Pilih Rekening Penerima --</option>
              @foreach($bankAccountList as $key => $bank)
                <option 
                  value="{{ $bank['value'] }}" 
                  {{ old('bank_account', 'operasional_dakwah') == $bank['value'] ? 'selected' : '' }}
                  data-bank="{{ $bank['bank_name'] }}"
                  data-account="{{ $bank['account_number'] }}"
                  data-name="{{ $bank['account_name'] }}"
                  data-desc="{{ $bank['description'] }}"
                  data-label="{{ $bank['label'] }}"
                >
                  ⚡ {{ $bank['label'] }} ({{ $bank['bank_name'] }} - {{ $bank['account_number'] }})
                </option>
              @endforeach
            </select>

            <!-- Detail Rekening Preview Box -->
            <div id="bank-detail-box" class="bank-info-card mt-2">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="font-weight-bold text-dark text-xs" id="bank-card-title">⚡ OPERASIONAL DAKWAH</span>
                <span class="badge bg-gradient-success text-xxs font-monospace" id="bank-card-acc">BSI 7179 42 4422</span>
              </div>
              <div class="text-xs text-secondary mb-1">
                a.n. <strong class="text-dark" id="bank-card-owner">MUTIARA HIKMAH OFFICIAL</strong>
              </div>
              <div class="text-xxs text-muted" id="bank-card-desc">
                ► Operasional Studio Dakwah, Program Dakwah & Tim Dakwah
              </div>
            </div>

            <!-- Custom Bank Account Form -->
            <div id="custom-bank-wrapper" class="p-3 bg-gray-100 rounded-3 mt-2 border" style="{{ old('bank_account') == 'lainnya' ? 'display: block;' : 'display: none;' }}">
              <div class="row g-2">
                <div class="col-12 col-md-4">
                  <input type="text" name="custom_bank_name" class="form-control form-control-sm" placeholder="Nama Bank (misal: BSI, Mandiri)" value="{{ old('custom_bank_name', 'Bank BSI') }}">
                </div>
                <div class="col-12 col-md-4">
                  <input type="text" name="custom_account_number" class="form-control form-control-sm" placeholder="Nomor Rekening" value="{{ old('custom_account_number', '') }}">
                </div>
                <div class="col-12 col-md-4">
                  <input type="text" name="custom_account_name" class="form-control form-control-sm" placeholder="Atas Nama (a.n.)" value="{{ old('custom_account_name', '') }}">
                </div>
              </div>
            </div>
          </div>
  
          <x-form-input
            label="Jumlah Donasi"
            name="amount"
            type="number"
            placeholder="isikan jumlah donasi / kosongkan jika donasi berbentuk barang"
            value="{{ old('amount') }}"
          />
          
          <x-form-textarea
            label="Catatan"
            name="note"
            placeholder="isikan catatan"
            value="{{ old('note') }}"
          />

          <div class="form-group mb-3">
            <label class="form-control-label">Bukti Transfer (Opsional)</label>
            <input 
              type="file" 
              name="proof_image" 
              class="form-control @error('proof_image') is-invalid @enderror" 
              accept="image/*"
            >
            @error('proof_image')
              <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
            @enderror
          </div>
  
          <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary">Tambah Data</button>
            <button type="button" class="btn btn-secondary" @click="resetDonor()">Ganti Donatur</button>
          </div>
        </form>
      </div>

    </div>
  </x-card>

  <x-slot:addonscript>
    <script defer src="https://unpkg.com/alpinejs@3.9.1/dist/cdn.min.js"></script>

    <script>
      function Donation() {
        const initialDonor = @json($donor_data);
        return {
          isDonor: initialDonor && initialDonor !== 'hide' ? true : false,
          donorName: initialDonor && initialDonor !== 'hide' ? initialDonor.name : '{{ old("name", old("donor_name", "")) }}',
          donorUuid: initialDonor && initialDonor !== 'hide' ? initialDonor.uuid : '{{ old("uuid", "") }}',
          searchQuery: '',
          results: [],
          showDropdown: false,
          loading: false,

          init() {
            if (this.isDonor) {
              this.$nextTick(() => {
                updateBankDisplay();
              });
            }
          },

          searchDonors() {
            const q = this.searchQuery.trim();
            if (q.length < 1) {
              this.results = [];
              this.showDropdown = false;
              return;
            }
            this.loading = true;
            this.showDropdown = true;
            fetch(`{{ route('donors.search.ajax') }}?q=${encodeURIComponent(q)}`)
              .then(res => res.json())
              .then(data => {
                this.results = data || [];
                this.loading = false;
              })
              .catch(() => {
                this.loading = false;
              });
          },

          selectDonor(donor) {
            this.donorName = donor.name;
            this.donorUuid = donor.uuid;
            this.isDonor = true;
            this.showDropdown = false;
            this.searchQuery = '';
            this.$nextTick(() => {
              updateBankDisplay();
            });
          },

          resetDonor() {
            this.isDonor = false;
            this.donorName = '';
            this.donorUuid = '';
            this.searchQuery = '';
            this.results = [];
            this.showDropdown = false;
          }
        }
      }

      function updateBankDisplay() {
        const selectBankAccount = document.getElementById('select-bank-account');
        const bankDetailBox = document.getElementById('bank-detail-box');
        const customBankWrapper = document.getElementById('custom-bank-wrapper');
        const bankCardTitle = document.getElementById('bank-card-title');
        const bankCardAcc = document.getElementById('bank-card-acc');
        const bankCardOwner = document.getElementById('bank-card-owner');
        const bankCardDesc = document.getElementById('bank-card-desc');

        if (!selectBankAccount) return;
        const opt = selectBankAccount.selectedOptions[0];
        if (!opt || !opt.value) {
          if (bankDetailBox) bankDetailBox.style.display = 'none';
          if (customBankWrapper) customBankWrapper.style.display = 'none';
          return;
        }

        if (opt.value === 'lainnya') {
          if (bankDetailBox) bankDetailBox.style.display = 'none';
          if (customBankWrapper) customBankWrapper.style.display = 'block';
        } else {
          if (bankDetailBox) bankDetailBox.style.display = 'block';
          if (customBankWrapper) customBankWrapper.style.display = 'none';
          
          const label = opt.getAttribute('data-label') || '';
          const accNo = opt.getAttribute('data-account') || '';
          const accName = opt.getAttribute('data-name') || '';
          const desc = opt.getAttribute('data-desc') || '';

          if (bankCardTitle) bankCardTitle.textContent = `⚡ ${label}`;
          if (bankCardAcc) bankCardAcc.textContent = `BSI ${accNo}`;
          if (bankCardOwner) bankCardOwner.textContent = accName;
          if (bankCardDesc) bankCardDesc.textContent = `► ${desc}`;
        }
      }
    </script>
  </x-slot>

</x-layouts.app>
