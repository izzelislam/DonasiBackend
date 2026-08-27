<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'phone_number',
        'address',
        'receipt_footer',
        'receipt_template',
    ];

    protected $appends = [
        'image_url',
        'receipt_footer_display',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return url($this->image);
        } else {
            return '-';
        }
    }

    public function getReceiptFooterDisplayAttribute()
    {
        if (!empty($this->receipt_footer)) {
            return $this->receipt_footer;
        }

        $orgName = $this->name ?? 'mutiara hikmah official';
        return "Terima kasih telah menyalurkan donasi melalui {$orgName}. Semoga layanan kami mendatangkan manfaat bagi anda.";
    }
}
