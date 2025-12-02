<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsActivity;

class Book extends Model
{
    use HasFactory, LogsActivity;

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

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function activeSubmissions()
    {
        return $this->hasMany(Submission::class)
            ->whereIn('status', ['created', 'overdue']);
    }

    /**
     * Check if book is available for submission
     */
    public function isAvailable(): bool
    {
        return !$this->activeSubmissions()->exists();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function activeReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'active');
    }

    public function availabilityAlerts()
    {
        return $this->hasMany(BookAvailabilityAlert::class);
    }

    public function activeAvailabilityAlerts()
    {
        return $this->hasMany(BookAvailabilityAlert::class)->where('notified', false);
    }
}
