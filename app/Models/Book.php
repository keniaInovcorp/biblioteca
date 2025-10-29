<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'isbn',
        'name',
        'publisher_id',
        'bibliography',
        'cover_image_path',
        'price',
    ];

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    // Relação com Authors - belongsToMany - many-to-many
    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image_path ? Storage::url($this->cover_image_path) : null;
    }
}
