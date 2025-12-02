<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsActivity;

class Publisher extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'name',
        'logo_path',
    ];

    // Obtem a URL pública do logo
    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? Storage::url($this->logo_path) : null;
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
