<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderProduct $orderProduct): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderProduct $orderProduct): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteProduct(User $user, Order $order, OrderProduct $orderProduct): bool
    {
        return ($user->id === $order->user_id && $orderProduct->order_id === $order->id) || $user->role_id === 1;
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrderProduct $orderProduct): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrderProduct $orderProduct): bool
    {
        return false;
    }
}
