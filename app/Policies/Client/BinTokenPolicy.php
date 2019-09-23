<?php

namespace App\Policies\Client;

use App\Models\BinToken;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BinTokenPolicy
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

    public function own(User $user, BinToken $token)
    {
        if ($user && $token->auth_model == User::class)
        {
            return $user->id === $token->auth_id;
        }
        return false;
    }
}
