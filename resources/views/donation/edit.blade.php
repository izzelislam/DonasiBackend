@php
    $donor_data = isset($donor) ? $donor : 'hide';
    $bankAccountList = \App\Enums\BankAccount::toArray();
    $currentBank = $donation->bank_account ?: 'operasional_dakwah';
    $currentCreatedAt = old('created_at', optional($donation->created_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i'));
@endphp
<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Donasi"
      page="Edit"
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
      transition: all 0.2s ease;
    }
  </style>

  <x-card title="Edit data donasi">
    <div x-data="Donation()">
      <form class="col-12 col-md-8" method="POST" action="{{ $action }}" enctype="multipart/form-data">
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
            placeholder="nama penerima"
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

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" value="" id="fcustomCheck1" x-on:change="Change()">
          <label class="custom-control-label" for="fcustomCheck1">Ubah penerima donasi</label>
        </div>

        <!-- Tanggal & Waktu Transaksi -->
        <div class="form-group mb-3">
          <label class="form-control-label text-dark font-weight-bold" for="created_at">
            <i class="fas fa-calendar-alt text-primary me-1"></i> Tanggal & Waktu Transaksi <span class="text-danger">*</span>
          </label>
          <input 
            type="datetime-local" 
            name="created_at" 
            id="created_at" 
            class="form-control @error('created_at') is-invalid @enderror" 
            value="{{ $currentCreatedAt }}" 
            required
          >
          <small class="form-text text-muted">
            Ubah tanggal dan waktu jika diperlukan untuk menyesuaikan tanda terima dan laporan.
          </small>
          @error('created_at')
            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <x-form-select
          label="Tujuan"
          name="type"
          :default=" ['value' => $donation->type ?? '', 'label' => strtoupper($donation->type ?? '')]"
          :options="['zakat' => 'ZAKAT', 'infaq' => 'INFAQ', 'shodaqoh' => 'SHODAQOH', 'wakaf' => 'WAKAF']"
        />

        <!-- Rekening Bank Penerima -->
        <div class="form-group mb-3">
          <label class="form-control-label text-dark font-weight-bold">
            Rekening Bank Penerima <span class="text-danger">*</span>
          </label>
          <select name="bank_account" id="select-bank-account" class="form-control @error('bank_account') is-invalid @enderror" required onchange="updateEditBankDisplay()">
            <option value="">-- Pilih Rekening Penerima --</option>
            @foreach($bankAccountList as $key => $bank)
              <option 
                value="{{ $bank['value'] }}" 
                {{ old('bank_account', $donation->bank_account ?? 'operasional_dakwah') == $bank['value'] ? 'selected' : '' }}
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
          @error('bank_account')
            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
          @enderror

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

          <!-- Custom Bank Account Form (Muncul jika pilih 'Lainnya') -->
          <div id="custom-bank-wrapper" class="p-3 bg-gray-100 rounded-3 mt-2 border" style="{{ old('bank_account', $donation->bank_account) == 'lainnya' ? 'display: block;' : 'display: none;' }}">
            <div class="text-xs font-weight-bold text-dark mb-2">
              <i class="fas fa-edit me-1 text-primary"></i> Detail Rekening Tambahan / Custom:
            </div>
            <div class="row g-2">
              <div class="col-12 col-md-4">
                <input type="text" name="custom_bank_name" id="custom-bank-name" class="form-control form-control-sm" placeholder="Nama Bank (misal: BSI, Mandiri)" value="{{ old('custom_bank_name', $donation->bank_name ?: 'Bank BSI') }}">
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="custom_account_number" id="custom-account-number" class="form-control form-control-sm" placeholder="Nomor Rekening" value="{{ old('custom_account_number', $donation->account_number) }}">
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="custom_account_name" id="custom-account-name" class="form-control form-control-sm" placeholder="Atas Nama (a.n.)" value="{{ old('custom_account_name', $donation->account_name) }}">
              </div>
            </div>
          </div>
        </div>

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
          placeholder="isikan catatan / doa"
          value="{{ $donation->note ?? '' }}"
        />

        <div class="form-group mb-3">
          <label class="form-control-label">Bukti Transfer (Opsional)</label>
          @if($donation->proof_image && file_exists(public_path($donation->proof_image)))
            <div class="mb-2">
              <a href="{{ asset($donation->proof_image) }}" target="_blank">
                <img src="{{ asset($donation->proof_image) }}" alt="Bukti Transfer" class="img-thumbnail" style="max-height: 120px; border-radius: 8px;">
              </a>
              <div class="text-xs text-muted mt-1">Klik gambar untuk memperbesar. Upload file baru untuk mengganti.</div>
            </div>
          @endif
          <input 
            type="file" 
            name="proof_image" 
            class="form-control @error('proof_image') is-invalid @enderror" 
            accept="image/*"
          >
          <small class="form-text text-muted">Format yang didukung: JPG, PNG, WEBP, SVG (Maks. 5MB).</small>
          @error('proof_image')
            <span class="invalid-feedback d-block">
              <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>

        <button class="btn btn-primary mt-2">Update Data</button>
      </form>
    </div>
  </x-card>

  <x-slot:addonscript>
    <script>
      function updateEditBankDisplay() {
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
          const bankName = opt.getAttribute('data-bank') || '';
          const accNo = opt.getAttribute('data-account') || '';
          const accName = opt.getAttribute('data-name') || '';
          const desc = opt.getAttribute('data-desc') || '';

          if (bankCardTitle) bankCardTitle.textContent = `⚡ ${label}`;
          if (bankCardAcc) bankCardAcc.textContent = `BSI ${accNo}`;
          if (bankCardOwner) bankCardOwner.textContent = accName;
          if (bankCardDesc) bankCardDesc.textContent = `► ${desc}`;
        }
      }

      document.addEventListener('DOMContentLoaded', function() {
        updateEditBankDisplay();
      });

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
            if (recipent) recipent.checked = false;
          },
        }
      }
    </script>
  </x-slot>

</x-layouts.app>
