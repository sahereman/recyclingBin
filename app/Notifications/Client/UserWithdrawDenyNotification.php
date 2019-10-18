<?php

namespace App\Notifications\Client;

use App\Models\UserWithdraw;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserWithdrawDenyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $withdraw;

    public function __construct(UserWithdraw $withdraw)
    {
        $this->withdraw = $withdraw;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $title = '提现失败';
        $info = '很抱歉您申请的提现未通过审核,提现金额' . $this->withdraw->money . '元,失败原因:' . $this->withdraw->reason . ',请修改后重新提交申请';
        $relation_model = $this->withdraw->getMorphClass();
        $relation_id = $this->withdraw->id;
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
