<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CartPolicy
{
    /**
     * Determine whether the user can view any carts.
     * Only citizens can view their own cart.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('citizen');
    }

    /**
     * Determine whether the user can view the cart.
     * Only citizens can view their own cart.
     */
    public function view(User $user, Cart $cart): bool
    {
        return $user->hasRole('citizen') && $cart->user_id === $user->id;
    }

    /**
     * Determine whether the user can add items to cart.
     * Only citizens can add items to cart.
     */
    public function addItem(User $user): bool
    {
        return $user->hasRole('citizen');
    }

    /**
     * Determine whether the user can update items in cart.
     * Only citizens can update their own cart.
     */
    public function updateItem(User $user, Cart $cart): bool
    {
        return $user->hasRole('citizen') && $cart->user_id === $user->id;
    }

    /**
     * Determine whether the user can remove items from cart.
     * Only citizens can remove items from their own cart.
     */
    public function removeItem(User $user, Cart $cart): bool
    {
        return $user->hasRole('citizen') && $cart->user_id === $user->id;
    }

    /**
     * Determine whether the user can clear the cart.
     * Only citizens can clear their own cart.
     */
    public function clearCart(User $user, Cart $cart): bool
    {
        return $user->hasRole('citizen') && $cart->user_id === $user->id;
    }
}

