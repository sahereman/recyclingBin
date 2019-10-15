<?php

namespace App\Http\Controllers\Clean;


use App\Http\Requests\Clean\PasswordResetRequest;
use App\Http\Requests\Clean\WithdrawUnionPayRequest;
use App\Http\Requests\Request;
use App\Models\Recycler;
use App\Models\RecyclerDeposit;
use App\Models\RecyclerMoneyBill;
use App\Models\RecyclerPayment;
use App\Models\RecyclerWithdraw;
use App\Notifications\Clean\RecyclerDepositSuccessNotification;
use App\Transformers\Clean\BinTransformer;
use App\Transformers\Clean\NotificationTransformer;
use App\Transformers\Clean\RecyclerMoneyBillTransformer;
use App\Transformers\Clean\RecyclerTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PaymentsController extends Controller
{

    public function wechatNotify(Request $request)
    {
        $app = app('wechat.payment.clean');
        $app['request'] = $request;

        try
        {
            $response = $app->handlePaidNotify(function ($message, $fail) {

                // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
                $payment = RecyclerPayment::where('sn', $message['out_trade_no'])->first();

                if ($payment == null)
                {
                    // 订单不存在
                    return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                }

                if ($payment->paid_at != null)
                {   // 订单已经支付过了
                    return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                }

                ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
                if ($message['return_code'] === 'SUCCESS')
                { // return_code 表示通信状态，不代表支付状态

                    // 用户是否支付成功
                    if (array_get($message, 'result_code') === 'SUCCESS' && $payment->amount == bcdiv($message['total_fee'], 100, 2))
                    {
                        // 支付成功处理
                        switch ($payment->related_model)
                        {
                            case RecyclerDeposit::class:
                                // 更新支付成功时间
                                $payment->update([
                                    'payment_sn' => str_random(16),
                                    'paid_at' => now(),
                                ]);

                                // 修改充值状态
                                $deposit = RecyclerDeposit::where('payment_id', $payment->id)->first();
                                $deposit->status = RecyclerDeposit::STATUS_COMPLETED;

                                // 生成账单
                                RecyclerMoneyBill::change($deposit->recycler, RecyclerMoneyBill::TYPE_RECYCLER_DEPOSIT, $deposit->money, $deposit);

                                // 通知用户
                                $deposit->recycler->notify(new RecyclerDepositSuccessNotification($deposit));
                                break;
                            default:
                                Log::error('订单关联异常');
                                return $fail('订单关联异常');
                                break;
                        }


                        return true;// 返回处理完成
                    } else
                    {
                        // 用户支付失败
                        Log::error('订单金额异常');
                        return $fail('订单金额异常');
                    }
                } else
                {
                    Log::error('通信失败，请稍后再通知我');
                    return $fail('通信失败，请稍后再通知我');
                }
            });

            // $response->send(); // Laravel 里请使用：return $response;
            return $response;
        } catch (\Exception $e)
        {
            Log::error($e);
            return '请传入xml';
        }
    }


}