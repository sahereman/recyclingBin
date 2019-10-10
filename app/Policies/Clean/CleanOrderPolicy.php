<?php

namespace App\Policies\Clean;

use App\Models\CleanOrder;
use App\Models\Recycler;
use Illuminate\Auth\Access\HandlesAuthorization;

class CleanOrderPolicy
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

    public function own(Recycler $recycler, CleanOrder $order)
    {
        if ($recycler && $order)
        {
            return $recycler->id === $order->recycler_id;
        }
        return false;
    }
}
