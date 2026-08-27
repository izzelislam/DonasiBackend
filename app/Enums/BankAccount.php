<?php

namespace App\Enums;

enum BankAccount: string
{
    case OPERASIONAL_DAKWAH = 'operasional_dakwah';
    case PEMBANGUNAN_MASJID = 'pembangunan_masjid';
    case WAKAF_JARIYAH = 'wakaf_jariyah';
    case ZAKAT_MAAL = 'zakat_maal';
    case DONASI_PROGRAM_SOSIAL = 'donasi_program_sosial';
    case LAINNYA = 'lainnya';

    public function label(): string
    {
        return match($this) {
            self::OPERASIONAL_DAKWAH => 'OPERASIONAL DAKWAH',
            self::PEMBANGUNAN_MASJID => 'PEMBANGUNAN MASJID',
            self::WAKAF_JARIYAH => 'WAKAF JARIYAH',
            self::ZAKAT_MAAL => 'ZAKAT MAAL',
            self::DONASI_PROGRAM_SOSIAL => 'DONASI PROGRAM SOSIAL',
            self::LAINNYA => 'LAINNYA',
        };
    }

    public function bankName(): string
    {
        return match($this) {
            self::OPERASIONAL_DAKWAH,
            self::PEMBANGUNAN_MASJID,
            self::WAKAF_JARIYAH,
            self::ZAKAT_MAAL,
            self::DONASI_PROGRAM_SOSIAL => 'Bank BSI (451)',
            self::LAINNYA => 'Lainnya',
        };
    }

    public function accountNumber(): string
    {
        return match($this) {
            self::OPERASIONAL_DAKWAH => '7179 42 4422',
            self::PEMBANGUNAN_MASJID => '2200 22 0245',
            self::WAKAF_JARIYAH => '7179 42 5507',
            self::ZAKAT_MAAL => '7179 42 7898',
            self::DONASI_PROGRAM_SOSIAL => '7179 42 6468',
            self::LAINNYA => '-',
        };
    }

    public function accountName(): string
    {
        return match($this) {
            self::OPERASIONAL_DAKWAH => 'MUTIARA HIKMAH OFFICIAL',
            self::PEMBANGUNAN_MASJID => 'MASJID MUTIARA HIKMAH',
            self::WAKAF_JARIYAH => 'WAKAF JARIYAH MUTIARA HIKMAH',
            self::ZAKAT_MAAL => 'ZAKAT MAAL MUTIARA HIKMAH',
            self::DONASI_PROGRAM_SOSIAL => 'MUTIARA HIKMAH PEDULI',
            self::LAINNYA => 'Lainnya',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::OPERASIONAL_DAKWAH => 'Operasional Studio Dakwah, Program Dakwah & Tim Dakwah',
            self::PEMBANGUNAN_MASJID => 'Pembangunan Masjid Mutiara Hikmah & Tahfizh Center Indonesia',
            self::WAKAF_JARIYAH => 'Pengadaan Asset Dakwah',
            self::ZAKAT_MAAL => 'Khusus Penyaluran Zakat Maal',
            self::DONASI_PROGRAM_SOSIAL => 'Peduli Musibah & Bencana, Penyaluran Dana Riba & Syubhat',
            self::LAINNYA => 'Rekening / Penyaluran Donasi Lainnya',
        };
    }

    public function formattedReceipt(): string
    {
        if ($this === self::LAINNYA) {
            return '-';
        }
        return "BSI {$this->accountNumber()}";
    }

    public static function toArray(): array
    {
        $list = [];
        foreach (self::cases() as $case) {
            $list[$case->value] = [
                'value' => $case->value,
                'label' => $case->label(),
                'bank_name' => $case->bankName(),
                'account_number' => $case->accountNumber(),
                'account_name' => $case->accountName(),
                'description' => $case->description(),
                'formatted_receipt' => $case->formattedReceipt(),
            ];
        }
        return $list;
    }

    public static function getDetails(?string $value): ?array
    {
        if (!$value) return null;
        $case = self::tryFrom($value);
        if (!$case) return null;
        return [
            'value' => $case->value,
            'label' => $case->label(),
            'bank_name' => $case->bankName(),
            'account_number' => $case->accountNumber(),
            'account_name' => $case->accountName(),
            'description' => $case->description(),
            'formatted_receipt' => $case->formattedReceipt(),
        ];
    }
}
