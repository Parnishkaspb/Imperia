<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
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
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id || in_array($user->role_id, [1, 2, 3, 5]) ;
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
    public function update(User $user, Order $order): bool
    {
        return $user->id === $order->user_id || in_array($user->role_id, [1, 2, 3, 5]) ;
    }

    /**
     * Determine whether the user can updatePrices the model.
     * (Сделано для того, чтобы только сам человек или админ мог поменять: Кол-во товара, цену за которую купили, цену за которую продали.
     * Никто кроме 2-ух человек не может этого сдлетаь)
     */
    public function updatePrices(User $user, Order $order): bool
    {
        return $user->id === $order->user_id || $user->role_id === 1;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order)
    {
        return $user->id === $order->user_id || $user->role_id === 1;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }
}
