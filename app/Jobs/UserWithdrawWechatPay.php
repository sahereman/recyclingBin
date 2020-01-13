<?php

namespace App\Jobs;

use App\Models\UserMoneyBill;
use App\Models\UserWithdraw;
use App\Notifications\Client\UserWithdrawWechatSuccessNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UserWithdrawWechatPay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $withdraw;

    public function __construct(UserWithdraw $withdraw)
    {
        $this->withdraw = $withdraw;
    }

    public function handle()
    {
        $user = $this->withdraw->user;

        try
        {
            DB::transaction(function () use ($user) {
                $withdraw = UserWithdraw::lockForUpdate()->find($this->withdraw->id);

                if (!$withdraw)
                {
                    throw new \Exception("UserWithdraw::lockForUpdate()->find($this->withdraw->id)");
                }

                if ($withdraw->type != UserWithdraw::TYPE_WECHAT)
                {
                    throw new \Exception("$withdraw->type != UserWithdraw::TYPE_WECHAT");
                }

                if ($withdraw->status != UserWithdraw::STATUS_WAIT)
                {
                    throw new \Exception("$withdraw->status != UserWithdraw::STATUS_WAIT");
                }

                $app = app('wechat.payment.default');

                $app->transfer->toBalance([
                    'partner_trade_no' => $withdraw->sn, // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
                    'openid' => $user->wx_openid,
                    'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
                    're_user_name' => '', // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
                    'amount' => (int)bcmul($withdraw->money, 100), // 企业付款金额，单位为分
                    'desc' => '垃圾分类投递奖励金', // 企业付款操作说明信息。必填
                ]);

                $partnerTradeNo = $withdraw->sn;
                $trace = $app->transfer->queryBalanceOrder($partnerTradeNo);

                if ($trace['result_code'] == 'SUCCESS')
                {
                    $withdraw->status = UserWithdraw::STATUS_AGREE;

                    $withdraw->update([
                        'status' => UserWithdraw::STATUS_AGREE,
                        'checked_at' => now(),
                    ]);
                    $user->update([
                        'frozen_money' => bcsub($user->frozen_money, $withdraw->money, 2),
                    ]);
                    UserMoneyBill::change($user, UserMoneyBill::TYPE_USER_WITHDRAW, $withdraw->money, $withdraw);
                    $withdraw->user->notify(new UserWithdrawWechatSuccessNotification($withdraw));
                }
                $withdraw->trace = $trace;
                $withdraw->save();

            });

        } catch (\Exception $e)
        {
            Log::error($e);
        }
    }
}
