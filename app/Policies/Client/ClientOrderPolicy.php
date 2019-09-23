<?php

namespace App\Policies\Client;

use App\Models\ClientOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientOrderPolicy
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

    public function own(User $user, ClientOrder $order)
    {
        if ($user && $order)
        {
            return $user->id === $order->user_id;
        }
        return false;
    }
}
