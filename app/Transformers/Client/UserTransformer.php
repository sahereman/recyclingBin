<?php

namespace App\Transformers\Client;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'wx_openid' => $user->wx_openid,
            'name' => $user->name,
            'gender' => $user->gender,
            'phone' => $user->phone,
            'avatar_url' => $user->avatar_url,
            'money' => $user->money,

            'country' => $user->country,
            'province' => $user->province,
            'city' => $user->city,

            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }



}