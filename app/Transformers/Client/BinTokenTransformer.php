<?php

namespace App\Transformers\Client;

use App\Models\BinToken;
use League\Fractal\TransformerAbstract;

class BinTokenTransformer extends TransformerAbstract
{
    public function transform(BinToken $token)
    {
        return [
            'id' => $token->id,
            'bin_id' => $token->bin_id,
            'token' => $token->token,
            'related_model' => $token->related_model,
            'related_id' => $token->related_id,
            'auth_model' => $token->auth_model,
            'auth_id' => $token->auth_id,

            'created_at' => $token->created_at->toDateTimeString(),
            'updated_at' => $token->updated_at->toDateTimeString(),
        ];
    }


}