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

            'real_authenticated_at' => $user->real_authenticated_at->toDateTimeString(),
            'real_name' => $user->real_name,
            'real_id' => $user->real_id,

            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }


}