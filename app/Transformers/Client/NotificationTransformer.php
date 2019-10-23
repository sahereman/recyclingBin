<?php

namespace App\Transformers\Client;

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

            'read_at' => $notification->read_at ? $notification->read_at->toDateTimeString() : null,
            'created_at' => $notification->created_at->toDateTimeString(),
        ];
    }


}