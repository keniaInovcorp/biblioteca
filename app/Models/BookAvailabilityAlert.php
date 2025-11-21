<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookAvailabilityAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'notified',
        'notified_at',
    ];

    protected $casts = [
        'notified' => 'boolean',
        'notified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function markAsNotified()
    {
        $this->update([
            'notified' => true,
            'notified_at' => now(),
        ]);
    }
}
