<?php

namespace App\Transformers\Client;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    public function transform(DatabaseNotification $notification)
    {
        return [
            'title' => $notification->data['title'],
            'info' => $notification->data['info'],
            'relation_model' => $notification->data['relation_model'],
            'relation_id' => $notification->data['relation_id'],
            'link' => $notification->data['link'],

            'created_at' => $notification->created_at->toDateTimeString(),
        ];
    }


}