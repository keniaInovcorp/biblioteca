<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'book_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get subtotal for this item
     */
    public function getSubtotalAttribute(): float
    {
        $price = $this->book->price ?? 0.0;
        return $price * $this->quantity;
    }
}
