<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'cart_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Get total price of all items in cart
     */
    public function getTotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            $price = $item->book->price ?? 0.0;
            return $price * $item->quantity;
        });
    }

    /**
     * Get total number of items in cart
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
