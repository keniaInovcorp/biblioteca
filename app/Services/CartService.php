<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Get or create cart for user
     */
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    /**
     * Add book to cart or increment quantity if already exists
     */
    public function addItem(User $user, Book $book, int $quantity = 1): CartItem
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than 0');
        }

        $cart = $this->getOrCreateCart($user);

        $cartItem = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'book_id' => $book->id,
        ]);

        if ($cartItem->exists) {
            $cartItem->increment('quantity', $quantity);
        } else {
            $cartItem->quantity = $quantity;
            $cartItem->save();
        }

        return $cartItem->fresh();
    }

    /**
     * Update item quantity
     */
    public function updateItemQuantity(User $user, Book $book, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($user, $book);
        }

        $cart = $this->getOrCreateCart($user);
        
        return CartItem::where('cart_id', $cart->id)
            ->where('book_id', $book->id)
            ->update(['quantity' => $quantity]) > 0;
    }

    /**
     * Remove item from cart
     */
    public function removeItem(User $user, Book $book): bool
    {
        $cart = $this->getOrCreateCart($user);

        return CartItem::where('cart_id', $cart->id)
            ->where('book_id', $book->id)
            ->delete() > 0;
    }

    /**
     * Clear cart
     */
    public function clearCart(User $user): bool
    {
        $cart = $this->getOrCreateCart($user);

        return CartItem::where('cart_id', $cart->id)->delete() > 0;
    }

    /**
     * Get cart with items and relationships
     */
    public function getCart(User $user): ?Cart
    {
        return Cart::with(['items.book.publisher', 'items.book.authors'])
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Get cart item count (optimized query)
     */
    public function getItemCount(User $user): int
    {
        $cart = $this->getOrCreateCart($user);

        return (int) CartItem::where('cart_id', $cart->id)
            ->sum('quantity');
    }
}