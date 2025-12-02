<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsActivity;

class Author extends Model
{
    use HasFactory, LogsActivity;
    
    protected $fillable = [
        'name',
        'photo_path',
    ];

    // Get the public URL of the photo
    public function getPhotoUrlAttribute()
    {
        return $this->photo_path ? Storage::url($this->photo_path) : null;
    }

    // Relação com Books - belongsToMany - many-to-many
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_author');
    }
}
