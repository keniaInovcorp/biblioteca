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
     * Check if submission is active (created or overdue status)
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['created', 'overdue']);
    }

    /**
     * Check if submission is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || 
               ($this->status === 'created' && $this->expected_return_date < now()->startOfDay());
    }

    /**
     * Get the effective status (considers overdue calculation)
     */
    public function getEffectiveStatusAttribute(): string
    {
        if ($this->status === 'returned') {
            return 'returned';
        }

        // If created but past expected date, it's effectively overdue
        if ($this->status === 'created' && $this->expected_return_date < now()->startOfDay()) {
            return 'overdue';
        }

        return $this->status;
    }
}

