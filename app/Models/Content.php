<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'content'
    ];

    protected $appends = [
        'image_link'
    ];

    public function getImageLinkAttribute() {
        if ($this->image) {
            return url('storage/' . $this->image);
        } else {
            return '-';
        }
    }
}
