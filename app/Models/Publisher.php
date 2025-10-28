<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Publisher extends Model
{
    protected $fillable = [
        'name',
        'logo_path',
    ];

    // Obtem a URL pública do logo
    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? Storage::url($this->logo_path) : null;
    }
}
