@php
    $bgPath = public_path('template-bg.jpeg');
    $bgBase64 = file_exists($bgPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($bgPath)) : '';

    $months = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    $dateObj = \Carbon\Carbon::parse($donation->created_at);
    $formattedDate = $dateObj->format('d') . ' ' . $months[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y') . ' • ' . $dateObj->format('H:i:s');

    // Generate true cover-cropped proof image via GD so DomPDF never stretches the image
    $proofBase64 = null;
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

                $proofBase64 = 'data:image/jpeg;base64,' . base64_encode($data);
            }
        }
    }
    if (!$proofBase64 && $rawProofPath) {
        $imgExt = pathinfo($donation->proof_image, PATHINFO_EXTENSION);
        $mime = ($imgExt === 'png') ? 'image/png' : (($imgExt === 'webp') ? 'image/webp' : 'image/jpeg');
        $proofBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($rawProofPath));
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
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tanda Terima - {{ $donation->receipt_uid }}</title>
    <style>
        @page {
            margin: 0;
            size: 425pt 900pt;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background-color: #f8fafc;
            width: 425pt;
            height: 900pt;
        }
        .page-container {
            width: 425pt;
            height: 900pt;
            position: relative;
            background-color: #ffffff;
            @if($bgBase64)
            background-image: url('{{ $bgBase64 }}');
            background-repeat: no-repeat;
            background-size: 425pt 900pt;
            background-position: top center;
            @endif
        }
        .content-box {
            position: absolute;
            top: 130pt;
            left: 30pt;
            right: 30pt;
            bottom: 22pt;
        }
        
        .institution-header {
            text-align: center;
            margin-bottom: 9pt;
        }
        .institution-name {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.2;
        }
        .institution-address {
            font-size: 11pt;
            color: #64748b;
            margin-top: 1pt;
            line-height: 1.25;
        }
        .institution-phone {
            font-size: 11.5pt;
            color: #64748b;
            margin-top: 1pt;
        }

        .status-header {
            text-align: center;
            margin-bottom: 12pt;
        }
        .checkmark-circle {
            width: 42pt;
            height: 42pt;
            background-color: #00A859;
            border-radius: 50%;
            margin: 0 auto 4pt auto;
            text-align: center;
            line-height: 42pt;
            color: #ffffff;
            font-size: 22pt;
            font-weight: bold;
        }
        .status-title {
            font-size: 19pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 2pt 0;
            letter-spacing: -0.3pt;
            text-align: center;
        }
        .status-date {
            font-size: 12.5pt;
            color: #64748b;
            margin: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4pt;
        }
        .info-table td {
            padding: 3.5pt 0;
            vertical-align: top;
            font-size: 12.5pt;
        }
        .info-table .label {
            color: #475569;
            width: 38%;
        }
        .info-table .value {
            color: #0f172a;
            font-weight: bold;
            text-align: right;
            width: 62%;
        }
        .info-table .value-sub {
            font-size: 10.5pt;
            color: #64748b;
            font-weight: normal;
            margin-top: 1pt;
        }

        .nominal-box {
            border-top: 1px solid rgba(0,0,0,0.06);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            padding: 6pt 0;
            margin: 6pt 0;
        }
        .nominal-table {
            width: 100%;
        }
        .nominal-table .label {
            font-size: 13pt;
            color: #475569;
            font-weight: 500;
        }
        .nominal-table .value {
            font-size: 19pt;
            font-weight: 800;
            color: #0f172a;
            text-align: right;
        }

        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 4pt 0;
        }

        .proof-box {
            float: right;
            width: 200pt;
            height: 135pt;
            border-radius: 6pt;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            text-align: center;
        }
        .proof-box img {
            width: 200pt;
            height: 135pt;
            display: block;
        }

        .footer-note {
            position: absolute;
            bottom: 6pt;
            left: 10pt;
            right: 10pt;
            text-align: center;
            font-size: 10pt;
            color: #64748b;
            line-height: 1.35;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="content-box">
            <!-- Institution Header -->
            <div class="institution-header">
                <div class="institution-name">{{ $institutionName }}</div>
                @if(!empty($setting->address))
                    <div class="institution-address">{{ $setting->address }}</div>
                @endif
                <div class="institution-phone">{{ $setting->phone_number ?? 'Hotline Service : 0896-3003-4005' }}</div>
            </div>

            <!-- Status Header -->
            <div class="status-header">
                <div class="checkmark-circle">&#10003;</div>
                <div class="status-title">Transaksi Berhasil</div>
                <div class="status-date">{{ $formattedDate }}</div>
            </div>

            <!-- Nominal -->
            <div class="nominal-box">
                <table class="nominal-table">
                    <tr>
                        <td class="label">Nominal</td>
                        <td class="value">Rp {{ number_format($donation->amount, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            <!-- Detail Transaksi 1 -->
            <table class="info-table">
                <tr>
                    <td class="label">Pengirim</td>
                    <td class="value">
                        {{ $donation->donor->name ?? '-' }}
                        @if($donation->donor->regency)
                            <div class="value-sub">
                                {{ ucwords(strtolower($donation->donor->regency->name)) }}{{ $donation->donor->province ? ', ' . ucwords(strtolower($donation->donor->province->name)) : '' }}
                            </div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Catatan</td>
                    <td class="value" style="font-weight: normal; color: #334155; font-size: 11pt;">
                        {{ $donation->note ?: '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Penerima</td>
                    <td class="value">
                        {{ $institutionName }}
                        @if($setting->phone_number)
                            <div class="value-sub">{{ $setting->phone_number }}</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Rekening Penerima</td>
                    <td class="value">
                        {{ $bankNameDisplay }} {{ $bankNumberDisplay }}
                        @if($donation->account_name && $donation->account_name !== '-')
                            <div class="value-sub">a.n. {{ $donation->account_name }}</div>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="divider"></div>

            <!-- Detail Transaksi 2 -->
            <table class="info-table">
                <tr>
                    <td class="label">Nomor Struk</td>
                    <td class="value" style="font-family: monospace; font-size: 11.5pt;">{{ $donation->receipt_uid }}</td>
                </tr>
                <tr>
                    <td class="label">Tujuan</td>
                    <td class="value">{{ strtoupper($donation->type) }}</td>
                </tr>
                @if($proofBase64)
                <tr>
                    <td class="label" style="padding-top: 6pt;">Bukti Transfer</td>
                    <td class="value" style="padding-top: 6pt;">
                        <div class="proof-box">
                            <img src="{{ $proofBase64 }}" alt="Bukti Transfer">
                        </div>
                    </td>
                </tr>
                @endif
            </table>

            <!-- Footer Message -->
            <div class="footer-note">
                {{ $footerText }}
            </div>
        </div>
    </div>
</body>
</html>
