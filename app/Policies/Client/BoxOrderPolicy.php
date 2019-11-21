<?php

namespace App\Policies\Client;

use App\Models\BoxOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoxOrderPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function own(User $user, BoxOrder $order)
    {
        if ($user && $order)
        {
            return $user->id === $order->user_id;
        }
        return false;
    }
}
