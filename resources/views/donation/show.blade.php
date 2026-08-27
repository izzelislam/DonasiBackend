@php
    $bgPath = public_path('template-bg.jpeg');
    $bgData = file_exists($bgPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($bgPath)) : asset('template-bg.jpeg');
    
    $months = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    $dateObj = \Carbon\Carbon::parse($donation->created_at);
    $formattedDate = $dateObj->format('d') . ' ' . $months[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y') . ' • ' . $dateObj->format('H:i:s');
    
    // Generate true cover-cropped proof image via GD so all exports and previews match 100%
    $proofSrc = null;
    $rawProofPath = ($donation->proof_image && file_exists(public_path($donation->proof_image))) ? public_path($donation->proof_image) : null;
    if ($rawProofPath && extension_loaded('gd')) {
        $info = @getimagesize($rawProofPath);
        if ($info) {
            $srcW = $info[0];
            $srcH = $info[1];
            $type = $info[2];
            $srcImg = null;
            if ($type === IMAGETYPE_JPEG) $srcImg = @imagecreatefromjpeg($rawProofPath);
            elseif ($type === IMAGETYPE_PNG) $srcImg = @imagecreatefrompng($rawProofPath);
            elseif ($type === IMAGETYPE_WEBP) $srcImg = @imagecreatefromwebp($rawProofPath);

            if ($srcImg && $srcW > 0 && $srcH > 0) {
                $targetW = 780;
                $targetH = 520;
                $scale = max($targetW / $srcW, $targetH / $srcH);
                $newW = (int)($srcW * $scale);
                $newH = (int)($srcH * $scale);
                $cropX = (int)(($newW - $targetW) / 2);
                $cropY = (int)(($newH - $targetH) / 2);

                $resized = imagecreatetruecolor($newW, $newH);
                imagecopyresampled($resized, $srcImg, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

                $cropped = imagecreatetruecolor($targetW, $targetH);
                imagecopy($cropped, $resized, 0, 0, $cropX, $cropY, $targetW, $targetH);

                ob_start();
                imagejpeg($cropped, null, 92);
                $data = ob_get_clean();
                imagedestroy($srcImg);
                imagedestroy($resized);
                imagedestroy($cropped);

                $proofSrc = 'data:image/jpeg;base64,' . base64_encode($data);
            }
        }
    }
    if (!$proofSrc) {
        if ($donation->proof_image_base64) {
            $proofSrc = $donation->proof_image_base64;
        } elseif ($donation->proof_image) {
            $proofSrc = asset($donation->proof_image);
        }
    }

    $institutionName = $setting && !empty($setting->name) ? $setting->name : 'mutiara hikmah official';
    if (strcasecmp($institutionName, 'mutiara hikmah official') === 0) {
        $institutionName = 'mutiara hikmah official';
    }

    $bankNumberDisplay = $donation->account_number ?: ($setting->account_number ?? '-');
    $bankNameDisplay = $donation->bank_name ?: 'Bank BSI';
    $accountNameDisplay = $donation->account_name ?: $institutionName;

    $footerText = !empty($setting->receipt_footer)
        ? str_ireplace('Mutiara hikmah official', 'mutiara hikmah official', $setting->receipt_footer)
        : ('Terima kasih telah menyalurkan donasi melalui ' . $institutionName . '. Semoga layanan kami mendatangkan manfaat bagi anda.');
@endphp

<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Donasi"
      page="Detail"
      link="donations.index"
    />
  </x-slot>

  <style>
    .detail-table td {
      white-space: normal !important;
      word-break: break-word !important;
      overflow-wrap: anywhere !important;
    }
  </style>

  <div class="row">
    <!-- Kolom Kiri: Detail Data Donasi -->
    <div class="col-12 col-xl-7 mb-4">
      <div class="card mb-4 shadow-sm border-0">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <h6 class="mb-0 font-weight-bold">Detail data donasi</h6>
          <div class="d-flex gap-2">
            <a href="{{ route('donations.index') }}" class="btn btn-xs btn-outline-secondary mb-0">
              <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('donations.edit', $donation->id) }}" class="btn btn-xs btn-warning text-white mb-0">
              <i class="fas fa-edit me-1"></i> Edit
            </a>
          </div>
        </div>
        <div class="card-body px-4 py-3">
          <div class="row g-4">
            <!-- Informasi Donatur -->
            <div class="col-12 col-md-6">
              <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Informasi Donatur</h6>
              <table class="table table-borderless table-sm mb-0 detail-table" style="table-layout: fixed; width: 100%;">
                <tbody>
                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="width: 90px; vertical-align: top; white-space: nowrap !important;">Nama</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-dark font-weight-bold text-sm py-2" style="vertical-align: top;">
                      {{ $donation->donor->name ?? '-' }}
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">No. HP</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-dark font-weight-bold text-sm py-2" style="vertical-align: top;">
                      {{ $donation->donor->phone_number ?? '-' }}
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">Kecamatan</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-dark font-weight-bold text-sm py-2" style="vertical-align: top;">
                      {{ $donation->donor->district->name ?? '-' }}
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">Kabupaten</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-dark font-weight-bold text-sm py-2" style="vertical-align: top;">
                      {{ $donation->donor->regency->name ?? '-' }}
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">Provinsi</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-dark font-weight-bold text-sm py-2" style="vertical-align: top;">
                      {{ $donation->donor->province->name ?? '-' }}
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">Alamat</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-dark font-weight-bold text-sm py-2" style="vertical-align: top; line-height: 1.45;">
                      {{ $donation->donor->address ?? '-' }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Informasi Transaksi -->
            <div class="col-12 col-md-6">
              <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Informasi Transaksi</h6>
              <table class="table table-borderless table-sm mb-0 detail-table" style="table-layout: fixed; width: 100%;">
                <tbody>
                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="width: 110px; vertical-align: top; white-space: nowrap !important;">Nama Penerima</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-dark font-weight-bold text-sm py-2" style="vertical-align: top;">
                      {{ $donation->recipient }}
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">Tujuan</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-dark font-weight-bold text-sm py-2" style="vertical-align: top;">
                      <span class="badge badge-sm bg-gradient-info">{{ strtoupper($donation->type) }}</span>
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">Rekening Tujuan</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-dark text-sm py-2" style="vertical-align: top;">
                      @if($donation->account_number)
                        <div class="font-weight-bold text-dark">{{ $bankNameDisplay }} - {{ $bankNumberDisplay }}</div>
                        <div class="text-xxs text-muted">a.n. {{ $accountNameDisplay }}</div>
                      @else
                        <span class="text-muted text-xs">-</span>
                      @endif
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">Nominal</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-success font-weight-bolder text-base py-2" style="vertical-align: top;">
                      Rp {{ number_format($donation->amount, 0, ',', '.') }}
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">Tgl Diterima</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-dark font-weight-bold text-sm py-2" style="vertical-align: top;">
                      {{ $donation->created_at->format('d/m/Y H:i:s') }}
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">Catatan</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="text-dark text-sm py-2" style="vertical-align: top; line-height: 1.45;">
                      {{ $donation->note ?: '-' }}
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">Bukti Transfer</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="py-2" style="vertical-align: top;">
                      @if($donation->proof_image && file_exists(public_path($donation->proof_image)))
                        <a href="{{ asset($donation->proof_image) }}" target="_blank" class="d-inline-block">
                          <img src="{{ asset($donation->proof_image) }}" alt="Bukti Transfer" class="img-thumbnail" style="max-height: 70px; border-radius: 6px;">
                        </a>
                      @else
                        <span class="text-muted text-xs italic">-</span>
                      @endif
                    </td>
                  </tr>

                  <tr>
                    <td class="text-secondary text-sm ps-0 py-2" style="vertical-align: top; white-space: nowrap !important;">Invoice</td>
                    <td class="text-secondary text-sm py-2 px-1" style="width: 12px; vertical-align: top;">:</td>
                    <td class="py-2" style="vertical-align: top;">
                      <div class="d-flex flex-column gap-1">
                        <span class="font-weight-bold text-dark font-monospace text-sm">{{ $donation->receipt_uid }}</span>
                        <div class="d-flex flex-wrap gap-1 mt-2">
                          <button type="button" class="btn btn-xs btn-primary text-white mb-0 shadow-none" data-bs-toggle="modal" data-bs-target="#previewModal">
                            <i class="fas fa-eye me-1"></i> Preview
                          </button>
                          <button type="button" class="btn btn-xs btn-success text-white mb-0 shadow-none" onclick="downloadReceiptAsJPG()">
                            <i class="fas fa-image me-1"></i> JPG
                          </button>
                          <button type="button" class="btn btn-xs btn-danger text-white mb-0 shadow-none" onclick="downloadReceiptAsPDF()">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kolom Kanan: Preview Tanda Terima -->
    <div class="col-12 col-xl-5 mb-4">
      <div class="card shadow-sm border-0">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <h6 class="mb-0 font-weight-bold">Preview Tanda Terima</h6>
          <div class="d-flex gap-1">
            <button type="button" class="btn btn-xs btn-success text-white mb-0 shadow-none" onclick="downloadReceiptAsJPG()" title="Download JPG">
              <i class="fas fa-image me-1"></i> JPG
            </button>
            <button type="button" class="btn btn-danger btn-xs text-white mb-0 shadow-none" onclick="downloadReceiptAsPDF()" title="Download PDF">
              <i class="fas fa-file-pdf me-1"></i> PDF
            </button>
            <button type="button" class="btn btn-xs btn-primary text-white mb-0 shadow-none" onclick="printReceiptPreview()" title="Print">
              <i class="fas fa-print me-1"></i> Cetak
            </button>
          </div>
        </div>
        <div class="card-body p-3 d-flex justify-content-center">
          
          <!-- Receipt Display Phone Mockup -->
          <div class="receipt-preview-card" style="width: 100%; max-width: 420px; aspect-ratio: 755 / 1600; background-image: url('{{ $bgData }}'); background-size: 100% 100%; background-repeat: no-repeat; background-position: top center; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); box-sizing: border-box; padding: 25% 7% 4% 7%; font-family: Arial, Helvetica, sans-serif; color: #1e293b; display: flex; flex-direction: column; justify-content: flex-start; position: relative;">
            
            <!-- Institution Header -->
            <div style="text-align: center; margin-bottom: 8px;">
              <div style="font-size: 0.95rem; font-weight: 700; color: #0f172a; line-height: 1.2;">{{ $institutionName }}</div>
              @if(!empty($setting->address))
                <div style="font-size: 0.68rem; color: #64748b; margin-top: 1px; line-height: 1.25;">{{ $setting->address }}</div>
              @endif
              <div style="font-size: 0.72rem; color: #64748b; margin-top: 1px;">{{ $setting->phone_number ?? 'Hotline Service : 0896-3003-4005' }}</div>
            </div>

            <!-- Status Header -->
            <div style="text-align: center; margin-bottom: 3%;">
              <div style="width: 40px; height: 40px; background-color: #00A859; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 4px auto; box-shadow: 0 4px 10px rgba(0, 168, 89, 0.3); color: #ffffff; font-size: 22px; font-weight: 900; line-height: 40px;">
                &#10003;
              </div>
              <h2 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0 0 2px 0; letter-spacing: -0.2px; text-align: center;">
                Transaksi&nbsp;Berhasil
              </h2>
              <div style="font-size: 0.75rem; color: #64748b; margin: 0;">{{ $formattedDate }}</div>
            </div>

            <!-- 1. Nominal Transfer -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 5px 0; border-bottom: 1px solid rgba(0,0,0,0.06); margin-bottom: 5px;">
              <span style="font-size: 0.8rem; color: #475569; font-weight: 500;">Nominal</span>
              <span style="font-size: 1.15rem; font-weight: 800; color: #0f172a;">Rp&nbsp;{{ number_format($donation->amount, 0, ',', '.') }}</span>
            </div>

            <!-- 2. Pengirim, 3. Catatan, 4. Penerima, 5. Rekening Penerima -->
            <div style="margin-bottom: 2px;">
              <!-- 2. Pengirim -->
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 3px;">
                <span style="font-size: 0.76rem; color: #475569; flex-shrink: 0; min-width: 80px;">Pengirim</span>
                <div style="text-align: right; flex-grow: 1; word-break: break-word;">
                  <div style="font-size: 0.78rem; font-weight: 700; color: #0f172a; line-height: 1.2;">{{ $donation->donor->name ?? '-' }}</div>
                  @if($donation->donor->regency)
                    <div style="font-size: 0.66rem; color: #64748b; margin-top: 1px;">
                      {{ ucwords(strtolower($donation->donor->regency->name)) }}{{ $donation->donor->province ? ', ' . ucwords(strtolower($donation->donor->province->name)) : '' }}
                    </div>
                  @endif
                </div>
              </div>

              <!-- 3. Catatan -->
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 3px;">
                <span style="font-size: 0.76rem; color: #475569; flex-shrink: 0; min-width: 60px;">Catatan</span>
                <span style="font-size: 0.7rem; color: #334155; text-align: right; flex-grow: 1; word-break: break-word; line-height: 1.3;">{{ $donation->note ?: '-' }}</span>
              </div>

              <!-- 4. Penerima -->
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 3px;">
                <span style="font-size: 0.76rem; color: #475569; flex-shrink: 0; min-width: 80px;">Penerima</span>
                <div style="text-align: right; flex-grow: 1; word-break: break-word;">
                  <div style="font-size: 0.78rem; font-weight: 700; color: #0f172a; line-height: 1.2;">{{ $institutionName }}</div>
                  @if($setting->phone_number)
                    <div style="font-size: 0.66rem; color: #64748b; margin-top: 1px;">{{ $setting->phone_number }}</div>
                  @endif
                </div>
              </div>

              <!-- 5. Rekening Penerima -->
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 3px;">
                <span style="font-size: 0.76rem; color: #475569; flex-shrink: 0; min-width: 110px;">Rekening&nbsp;Penerima</span>
                <div style="text-align: right; flex-grow: 1; word-break: break-word;">
                  <div style="font-size: 0.73rem; font-weight: 700; color: #0f172a; line-height: 1.2;">
                    {{ $bankNameDisplay }} {{ $bankNumberDisplay }}
                  </div>
                  @if($donation->account_name && $donation->account_name !== '-')
                    <div style="font-size: 0.64rem; color: #64748b; margin-top: 1px;">a.n.&nbsp;{{ $donation->account_name }}</div>
                  @endif
                </div>
              </div>
            </div>

            <div style="height: 1px; background-color: #e2e8f0; margin: 3px 0;"></div>

            <!-- 6. Nomor Struk, 7. Tujuan, 8. Bukti Transfer -->
            <div style="margin-bottom: 2px;">
              <!-- 6. Nomor Struk -->
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                <span style="font-size: 0.76rem; color: #475569;">Nomor&nbsp;Struk</span>
                <span style="font-size: 0.7rem; font-weight: 600; color: #1e293b; font-family: monospace;">{{ $donation->receipt_uid }}</span>
              </div>
              <!-- 7. Tujuan -->
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                <span style="font-size: 0.76rem; color: #475569;">Tujuan</span>
                <span style="font-size: 0.7rem; font-weight: 600; color: #1e293b;">{{ strtoupper($donation->type) }}</span>
              </div>
              <!-- 8. Bukti Transfer -->
              @if($proofSrc)
                <div style="display: flex !important; justify-content: space-between !important; align-items: flex-start !important; gap: 8px; margin-top: 4px;">
                  <span style="font-size: 0.76rem; color: #475569; flex-shrink: 0; min-width: 90px; padding-top: 2px;">Bukti&nbsp;Transfer</span>
                  <div style="display: flex; justify-content: flex-end; flex-grow: 1;">
                    <a href="{{ $donation->proof_image ? asset($donation->proof_image) : $proofSrc }}" target="_blank" title="Klik untuk melihat bukti transfer penuh" style="display: block; width: 195px; height: 130px; border-radius: 6px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.08); background-color: #f8fafc; position: relative;">
                      <img src="{{ $proofSrc }}" alt="Bukti Transfer" style="width: 100%; height: 100%; display: block;">
                    </a>
                  </div>
                </div>
              @endif
            </div>

            <!-- Footer Message -->
            <div style="margin-top: auto; padding: 0 4%; text-align: center;">
              <p style="font-size: 0.63rem; color: #64748b; line-height: 1.35; margin: 0;">
                {{ $footerText }}
              </p>
            </div>

          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Modal Preview Tanda Terima -->
  <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-light py-2">
          <h6 class="modal-title font-weight-bold" id="previewModalLabel">Preview Tanda Terima Donasi</h6>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-3 d-flex justify-content-center bg-gray-100">
          <div class="receipt-modal-card" style="width: 100%; max-width: 440px; aspect-ratio: 755 / 1600; background-image: url('{{ $bgData }}'); background-size: 100% 100%; background-repeat: no-repeat; background-position: top center; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); box-sizing: border-box; padding: 25% 7% 4% 7%; font-family: Arial, Helvetica, sans-serif; color: #1e293b; display: flex; flex-direction: column; justify-content: flex-start; position: relative;">
            
            <!-- Institution Header -->
            <div style="text-align: center; margin-bottom: 8px;">
              <div style="font-size: 0.95rem; font-weight: 700; color: #0f172a; line-height: 1.2;">{{ $institutionName }}</div>
              @if(!empty($setting->address))
                <div style="font-size: 0.68rem; color: #64748b; margin-top: 1px; line-height: 1.25;">{{ $setting->address }}</div>
              @endif
              <div style="font-size: 0.72rem; color: #64748b; margin-top: 1px;">{{ $setting->phone_number ?? 'Hotline Service : 0896-3003-4005' }}</div>
            </div>

            <!-- Status Header -->
            <div style="text-align: center; margin-bottom: 3%;">
              <div style="width: 40px; height: 40px; background-color: #00A859; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 4px auto; box-shadow: 0 4px 10px rgba(0, 168, 89, 0.3); color: #ffffff; font-size: 22px; font-weight: 900; line-height: 40px;">
                &#10003;
              </div>
              <h2 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0 0 2px 0; text-align: center;">
                Transaksi&nbsp;Berhasil
              </h2>
              <div style="font-size: 0.75rem; color: #64748b; margin: 0;">{{ $formattedDate }}</div>
            </div>

            <!-- 1. Nominal -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 5px 0; border-bottom: 1px solid rgba(0,0,0,0.06); margin-bottom: 5px;">
              <span style="font-size: 0.8rem; color: #475569; font-weight: 500;">Nominal</span>
              <span style="font-size: 1.15rem; font-weight: 800; color: #0f172a;">Rp&nbsp;{{ number_format($donation->amount, 0, ',', '.') }}</span>
            </div>

            <!-- 2. Pengirim, 3. Catatan, 4. Penerima, 5. Rekening Penerima -->
            <div style="margin-bottom: 2px;">
              <!-- 2. Pengirim -->
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 3px;">
                <span style="font-size: 0.76rem; color: #475569; flex-shrink: 0; min-width: 80px;">Pengirim</span>
                <div style="text-align: right; flex-grow: 1; word-break: break-word;">
                  <div style="font-size: 0.78rem; font-weight: 700; color: #0f172a; line-height: 1.2;">{{ $donation->donor->name ?? '-' }}</div>
                  @if($donation->donor->regency)
                    <div style="font-size: 0.66rem; color: #64748b; margin-top: 1px;">
                      {{ ucwords(strtolower($donation->donor->regency->name)) }}{{ $donation->donor->province ? ', ' . ucwords(strtolower($donation->donor->province->name)) : '' }}
                    </div>
                  @endif
                </div>
              </div>

              <!-- 3. Catatan -->
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 3px;">
                <span style="font-size: 0.76rem; color: #475569; flex-shrink: 0; min-width: 60px;">Catatan</span>
                <span style="font-size: 0.7rem; color: #334155; text-align: right; flex-grow: 1; word-break: break-word; line-height: 1.3;">{{ $donation->note ?: '-' }}</span>
              </div>

              <!-- 4. Penerima -->
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 3px;">
                <span style="font-size: 0.76rem; color: #475569; flex-shrink: 0; min-width: 80px;">Penerima</span>
                <div style="text-align: right; flex-grow: 1; word-break: break-word;">
                  <div style="font-size: 0.78rem; font-weight: 700; color: #0f172a; line-height: 1.2;">{{ $institutionName }}</div>
                  @if($setting->phone_number)
                    <div style="font-size: 0.66rem; color: #64748b; margin-top: 1px;">{{ $setting->phone_number }}</div>
                  @endif
                </div>
              </div>

              <!-- 5. Rekening Penerima -->
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 3px;">
                <span style="font-size: 0.76rem; color: #475569; flex-shrink: 0; min-width: 110px;">Rekening&nbsp;Penerima</span>
                <div style="text-align: right; flex-grow: 1; word-break: break-word;">
                  <div style="font-size: 0.73rem; font-weight: 700; color: #0f172a; line-height: 1.2;">
                    {{ $bankNameDisplay }} {{ $bankNumberDisplay }}
                  </div>
                  @if($donation->account_name && $donation->account_name !== '-')
                    <div style="font-size: 0.64rem; color: #64748b; margin-top: 1px;">a.n.&nbsp;{{ $donation->account_name }}</div>
                  @endif
                </div>
              </div>
            </div>

            <div style="height: 1px; background-color: #e2e8f0; margin: 3px 0;"></div>

            <!-- 6. Nomor Struk, 7. Tujuan, 8. Bukti Transfer -->
            <div style="margin-bottom: 2px;">
              <!-- 6. Nomor Struk -->
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                <span style="font-size: 0.76rem; color: #475569;">Nomor&nbsp;Struk</span>
                <span style="font-size: 0.7rem; font-weight: 600; color: #1e293b; font-family: monospace;">{{ $donation->receipt_uid }}</span>
              </div>
              <!-- 7. Tujuan -->
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                <span style="font-size: 0.76rem; color: #475569;">Tujuan</span>
                <span style="font-size: 0.7rem; font-weight: 600; color: #1e293b;">{{ strtoupper($donation->type) }}</span>
              </div>
              <!-- 8. Bukti Transfer -->
              @if($proofSrc)
                <div style="display: flex !important; justify-content: space-between !important; align-items: flex-start !important; gap: 8px; margin-top: 4px;">
                  <span style="font-size: 0.76rem; color: #475569; flex-shrink: 0; min-width: 90px; padding-top: 2px;">Bukti&nbsp;Transfer</span>
                  <div style="display: flex; justify-content: flex-end; flex-grow: 1;">
                    <a href="{{ $donation->proof_image ? asset($donation->proof_image) : $proofSrc }}" target="_blank" title="Klik untuk melihat bukti transfer penuh" style="display: block; width: 195px; height: 130px; border-radius: 6px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.08); background-color: #f8fafc; position: relative;">
                      <img src="{{ $proofSrc }}" alt="Bukti Transfer" style="width: 100%; height: 100%; display: block;">
                    </a>
                  </div>
                </div>
              @endif
            </div>

            <!-- Footer Message -->
            <div style="margin-top: auto; padding: 0 4%; text-align: center;">
              <p style="font-size: 0.63rem; color: #64748b; line-height: 1.35; margin: 0;">
                {{ $footerText }}
              </p>
            </div>
          </div>
        </div>
        <div class="modal-footer py-2 justify-content-between">
          <button type="button" class="btn btn-secondary btn-sm mb-0" data-bs-dismiss="modal">Tutup</button>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-success btn-sm mb-0 text-white shadow-none" onclick="downloadReceiptAsJPG()">
              <i class="fas fa-image me-1"></i> JPG
            </button>
            <button type="button" class="btn btn-danger btn-sm mb-0 text-white shadow-none" onclick="downloadReceiptAsPDF()">
              <i class="fas fa-file-pdf me-1"></i> PDF
            </button>
            <button type="button" class="btn btn-primary btn-sm mb-0 text-white shadow-none" onclick="printReceiptPreview()">
              <i class="fas fa-print me-1"></i> Cetak
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Hidden 755x1600 Full-Res Capture Node for Pixel-Perfect JPG & PDF Export (230px top padding) -->
  <div style="position: fixed; left: -10000px; top: 0; width: 755px; height: 1600px; z-index: -100; pointer-events: none;">
    <div id="receipt-fullres" style="width: 755px; height: 1600px; background-image: url('{{ $bgData }}'); background-repeat: no-repeat; background-size: 755px 1600px; background-position: top center; box-sizing: border-box; padding: 230px 53px 40px 53px; font-family: Arial, Helvetica, sans-serif; color: #1e293b; background-color: #ffffff; display: flex; flex-direction: column; justify-content: flex-start; position: relative;">
      
      <!-- Institution Header -->
      <div style="text-align: center; margin-bottom: 16px;">
        <div style="font-size: 30px; font-weight: 700; color: #0f172a; line-height: 1.2;">{{ $institutionName }}</div>
        @if(!empty($setting->address))
          <div style="font-size: 21px; color: #64748b; margin-top: 2px; line-height: 1.25;">{{ $setting->address }}</div>
        @endif
        <div style="font-size: 22px; color: #64748b; margin-top: 2px;">{{ $setting->phone_number ?? 'Hotline Service : 0896-3003-4005' }}</div>
      </div>

      <!-- Status Header -->
      <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 80px; height: 80px; background-color: #00A859; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px auto; box-shadow: 0 8px 20px rgba(0, 168, 89, 0.3); color: #ffffff; font-size: 44px; font-weight: 900; line-height: 80px;">
          &#10003;
        </div>
        <h2 style="font-size: 36px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.4px; text-align: center;">
          Transaksi&nbsp;Berhasil
        </h2>
        <div style="font-size: 24px; color: #64748b; margin: 0;">{{ $formattedDate }}</div>
      </div>

      <!-- 1. Nominal -->
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 2px solid rgba(0,0,0,0.06); margin-bottom: 10px;">
        <span style="font-size: 25px; color: #475569; font-weight: 500;">Nominal</span>
        <span style="font-size: 36px; font-weight: 800; color: #0f172a;">Rp&nbsp;{{ number_format($donation->amount, 0, ',', '.') }}</span>
      </div>

      <!-- 2. Pengirim, 3. Catatan, 4. Penerima, 5. Rekening Penerima -->
      <div style="margin-bottom: 4px;">
        <!-- 2. Pengirim -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 6px;">
          <span style="font-size: 24px; color: #475569; flex-shrink: 0; min-width: 240px;">Pengirim</span>
          <div style="text-align: right; flex-grow: 1; word-break: break-word;">
            <div style="font-size: 25px; font-weight: 700; color: #0f172a; line-height: 1.2;">{{ $donation->donor->name ?? '-' }}</div>
            @if($donation->donor->regency)
              <div style="font-size: 21px; color: #64748b; margin-top: 2px;">
                {{ ucwords(strtolower($donation->donor->regency->name)) }}{{ $donation->donor->province ? ', ' . ucwords(strtolower($donation->donor->province->name)) : '' }}
              </div>
            @endif
          </div>
        </div>

        <!-- 3. Catatan -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 6px;">
          <span style="font-size: 24px; color: #475569; flex-shrink: 0; min-width: 200px;">Catatan</span>
          <span style="font-size: 22px; color: #334155; text-align: right; flex-grow: 1; word-break: break-word; line-height: 1.3;">{{ $donation->note ?: '-' }}</span>
        </div>

        <!-- 4. Penerima -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 6px;">
          <span style="font-size: 24px; color: #475569; flex-shrink: 0; min-width: 240px;">Penerima</span>
          <div style="text-align: right; flex-grow: 1; word-break: break-word;">
            <div style="font-size: 25px; font-weight: 700; color: #0f172a; line-height: 1.2;">{{ $institutionName }}</div>
            @if($setting->phone_number)
              <div style="font-size: 21px; color: #64748b; margin-top: 2px;">{{ $setting->phone_number }}</div>
            @endif
          </div>
        </div>

        <!-- 5. Rekening Penerima -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 6px;">
          <span style="font-size: 24px; color: #475569; flex-shrink: 0; min-width: 260px;">Rekening&nbsp;Penerima</span>
          <div style="text-align: right; flex-grow: 1; word-break: break-word;">
            <div style="font-size: 23px; font-weight: 700; color: #0f172a; line-height: 1.2;">
              {{ $bankNameDisplay }}&nbsp;{{ $bankNumberDisplay }}
            </div>
            @if($donation->account_name && $donation->account_name !== '-')
              <div style="font-size: 20px; color: #64748b; margin-top: 2px;">a.n.&nbsp;{{ $donation->account_name }}</div>
            @endif
          </div>
        </div>
      </div>

      <div style="height: 2px; background-color: #e2e8f0; margin: 6px 0;"></div>

      <!-- 6. Nomor Struk, 7. Tujuan, 8. Bukti Transfer -->
      <div style="margin-bottom: 4px;">
        <!-- 6. Nomor Struk -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
          <span style="font-size: 24px; color: #475569;">Nomor&nbsp;Struk</span>
          <span style="font-size: 22px; font-weight: 600; color: #1e293b; font-family: monospace;">{{ $donation->receipt_uid }}</span>
        </div>
        <!-- 7. Tujuan -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
          <span style="font-size: 24px; color: #475569;">Tujuan</span>
          <span style="font-size: 22px; font-weight: 600; color: #1e293b;">{{ strtoupper($donation->type) }}</span>
        </div>
        <!-- 8. Bukti Transfer (Exact 390x260 pixel perfect cover) -->
        @if($proofSrc)
          <div style="display: flex !important; justify-content: space-between !important; align-items: flex-start !important; gap: 16px; margin-top: 8px;">
            <span style="font-size: 24px; color: #475569; flex-shrink: 0; min-width: 240px; padding-top: 4px;">Bukti&nbsp;Transfer</span>
            <div style="display: flex; justify-content: flex-end; flex-grow: 1;">
              <div style="width: 390px; height: 260px; border-radius: 12px; overflow: hidden; border: 2px solid #cbd5e1; box-shadow: 0 4px 16px rgba(0,0,0,0.08); background-color: #f8fafc; position: relative;">
                <img src="{{ $proofSrc }}" alt="Bukti Transfer" style="width: 390px; height: 260px; display: block;">
              </div>
            </div>
          </div>
        @endif
      </div>

      <!-- Footer Message -->
      <div style="margin-top: auto; padding: 0 4%; text-align: center;">
        <p style="font-size: 20px; color: #64748b; line-height: 1.35; margin: 0;">
          {{ $footerText }}
        </p>
      </div>

    </div>
  </div>

  <!-- Html2Canvas and JsPDF for high-res export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <style>
    @media print {
      body * {
        visibility: hidden !important;
      }
      #receipt-fullres, #receipt-fullres * {
        visibility: visible !important;
      }
      #receipt-fullres {
        position: fixed !important;
        left: 50% !important;
        top: 0 !important;
        transform: translateX(-50%) scale(0.55) !important;
        transform-origin: top center !important;
        box-shadow: none !important;
        margin: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
    }
  </style>

  <script>
    function printReceiptPreview() {
      window.print();
    }

    async function downloadReceiptAsJPG() {
      const card = document.getElementById('receipt-fullres');
      if (!card) {
        alert('Elemen struk tidak ditemukan.');
        return;
      }
      
      try {
        const canvas = await html2canvas(card, {
          scale: 1,
          useCORS: true,
          allowTaint: false,
          backgroundColor: '#ffffff',
          width: 755,
          height: 1600,
          logging: false
        });
        
        const link = document.createElement('a');
        link.download = '{{ $donation->receipt_uid }}.jpg';
        link.href = canvas.toDataURL('image/jpeg', 0.95);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } catch (err) {
        console.error('Error generating JPG:', err);
        alert('Gagal mendownload JPG. Silakan gunakan tombol cetak/print.');
      }
    }

    async function downloadReceiptAsPDF() {
      const card = document.getElementById('receipt-fullres');
      if (!card) {
        window.open('{{ route("donations.print.receipt", ["uid" => $donation->receipt_uid]) }}', '_blank');
        return;
      }
      
      try {
        const canvas = await html2canvas(card, {
          scale: 1,
          useCORS: true,
          allowTaint: false,
          backgroundColor: '#ffffff',
          width: 755,
          height: 1600,
          logging: false
        });
        
        const imgData = canvas.toDataURL('image/jpeg', 0.95);
        if (window.jspdf && window.jspdf.jsPDF) {
          const { jsPDF } = window.jspdf;
          const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'pt',
            format: [425, 900]
          });
          
          pdf.addImage(imgData, 'JPEG', 0, 0, 425, 900);
          pdf.save('{{ $donation->receipt_uid }}.pdf');
        } else {
          window.open('{{ route("donations.print.receipt", ["uid" => $donation->receipt_uid]) }}', '_blank');
        }
      } catch (err) {
        console.error('Error generating PDF:', err);
        window.open('{{ route("donations.print.receipt", ["uid" => $donation->receipt_uid]) }}', '_blank');
      }
    }
  </script>

</x-layouts.app>
