<?php

namespace App\Notifications\Clean;

use App\Models\CleanOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CleanOrderCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(CleanOrder $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $title = '取货成功';
        $info = '恭喜您取货成功,取回货物消费' . $this->order->total . '元,前往"回收记录"页面查看回收详情';
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
