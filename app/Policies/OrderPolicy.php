<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

/**
 * Policy for controlling access to Order models.
 *
 * This policy defines authorization rules for viewing, creating,
 * updating, and deleting orders.
 */
class OrderPolicy
{
    /**
     * Determine whether the user can view any orders.
     *
     * @param User $user The authenticated user
     * @return bool True if the user can view any orders, false otherwise
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view a specific order.
     *
     * A user can view an order if:
     * - They are the owner of the order, or
     * - They are an admin (can create books)
     *
     * @param User $user The authenticated user
     * @param Order $order The order to view
     * @return bool True if the user can view the order, false otherwise
     */
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id || $user->can('create', \App\Models\Book::class);
    }

    /**
     * Determine whether the user can create orders.
     *
     * @param User $user The authenticated user
     * @return bool True if the user can create orders, false otherwise
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update a specific order.
     *
     * Only administrators (users who can create books) can update orders.
     *
     * @param User $user The authenticated user
     * @param Order $order The order to update
     * @return bool True if the user can update the order, false otherwise
     */
    public function update(User $user, Order $order): bool
    {
        return $user->can('create', \App\Models\Book::class);
    }

    /**
     * Determine whether the user can delete a specific order.
     *
     * Only administrators (users who can create books) can delete orders.
     *
     * @param User $user The authenticated user
     * @param Order $order The order to delete
     * @return bool True if the user can delete the order, false otherwise
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->can('create', \App\Models\Book::class);
    }
}
