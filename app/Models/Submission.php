<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;


    protected $fillable = [
        'request_number',
        'user_id',
        'book_id',
        'request_date',
        'expected_return_date',
        'received_at',
        'days_elapsed',
        'status',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'date',
        'expected_return_date' => 'date',
        'received_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Generate sequential request number
     */
    public static function generateRequestNumber(): string
    {
        $last = self::orderBy('id', 'desc')->first();
        $nextNumber = $last ? ((int) str_replace('REQ-', '', $last->request_number)) + 1 : 1;
        return 'REQ-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if submission is active (pending or active status)
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'active']);
    }
}

