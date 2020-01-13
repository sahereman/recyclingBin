<?php

namespace App\Notifications\Client;

use App\Models\UserWithdraw;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserWithdrawAgreeNotification extends Notification implements ShouldQueue
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
        $title = '提现成功';
        $info = '';
        $relation_model = $this->withdraw->getMorphClass();
        $relation_id = $this->withdraw->id;
        $link = '';

        switch ($this->withdraw->type)
        {
            case UserWithdraw::TYPE_UNION_PAY:
                $info = '恭喜您提现成功,提现金额' . $this->withdraw->money . '元已转入银行卡,银行账号:' . $this->withdraw->info['account'] . ',请注意查收';
                break;
            case UserWithdraw::TYPE_WECHAT:
                $info = '恭喜您提现成功,提现金额' . $this->withdraw->money . '元已转入您的微信钱包,请注意查收';
                break;
        }

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
