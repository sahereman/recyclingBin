<?php

namespace App\Transformers\Client;

use App\Models\User;
use App\Models\UserMoneyBill;
use League\Fractal\TransformerAbstract;

class UserMoneyBillTransformer extends TransformerAbstract
{
    public function transform(UserMoneyBill $bill)
    {
        return [
            'id' => $bill->id,
            'user_id' => $bill->user_id,
            'type' => $bill->type,
            'type_text' => $bill->type_text,
            'description' => $bill->description,
            'operator' => $bill->operator,
            'number' => $bill->number,
//            'related_model' => $bill->related_model,
//            'related_id'=> $bill->related_id,

            'created_at' => $bill->created_at->toDateTimeString(),
        ];
    }



}