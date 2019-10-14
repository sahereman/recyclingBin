<?php

namespace App\Notifications\Clean;

use App\Models\RecyclerDeposit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RecyclerDepositSuccessNotification extends Notification
{
    use Queueable;

    public $deposit;

    public function __construct(RecyclerDeposit $deposit)
    {
        $this->deposit = $deposit;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $title = '充值成功';
        $info = '恭喜您充值成功,充值金额' . $this->deposit->money . '元已存入余额,请注意查收';
        $relation_model = $this->deposit->getMorphClass();
        $relation_id = $this->deposit->id;
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
