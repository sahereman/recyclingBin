<?php

namespace App\Notifications\Client;

use App\Models\ClientOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ClientOrderCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(ClientOrder $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $title = '投递成功';
        $info = '恭喜您投递成功,获得奖励金' . $this->order->total . '元,前往"我的订单"页面查看投递详情';
        $relation_model = $this->order->getMorphClass();
        $relation_id = $this->order->id;
        $link = '';

        // 存入数据库里的数据
        return [
            'title' => $title,
            'info' => $info,
            'relation_model' => $relation_model,
            'relation_id' => $relation_id,
            'link' => $link,
        ];
    }
}
