<?php

namespace App\Http\Controllers\Clean;


use App\Http\Requests\Request;
use App\Models\RecyclerDeposit;
use App\Models\RecyclerMoneyBill;
use App\Models\RecyclerPayment;
use App\Notifications\Clean\RecyclerDepositSuccessNotification;
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

                // $message array
                //'appid' => 'wx907598*****',
                //'bank_type' => 'CFT',
                //'cash_fee' => '1',
                //'fee_type' => 'CNY',
                //'is_subscribe' => 'N',
                //'mch_id' => '1555991491',
                //'nonce_str' => '5da7e05ad1f5a',
                //'openid' => 'oN3zU5Lw-Defg3yCSx2N3Rmkn21Q',
                //'out_trade_no' => '20191017113034665466',
                //'result_code' => 'SUCCESS',
                //'return_code' => 'SUCCESS',
                //'sign' => 'EEFDFE15ED24C5BD107C279E07F65A9D',
                //'time_end' => '20191017113039',
                //'total_fee' => '1',
                //'trade_type' => 'JSAPI',
                //'transaction_id' => '4200000448201910179938317765',

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
                        // 更新支付成功时间
                        $payment->update([
                            'payment_sn' => array_get($message, 'transaction_id', ''),
                            'paid_at' => now(),
                        ]);

                        // 支付类型处理
                        switch ($payment->related_model)
                        {
                            case RecyclerDeposit::class:

                                // 修改充值状态
                                $deposit = RecyclerDeposit::where('payment_id', $payment->id)->first();
                                $deposit->status = RecyclerDeposit::STATUS_COMPLETED;

                                $recycler = $deposit->recycler;

                                // 增加用户余额
                                $recycler->update([
                                    'money' => bcadd($recycler->money, $deposit->money, 2)
                                ]);

                                // 生成账单
                                RecyclerMoneyBill::change($recycler, RecyclerMoneyBill::TYPE_RECYCLER_DEPOSIT, $deposit->money, $deposit);

                                // 通知用户
                                $recycler->notify(new RecyclerDepositSuccessNotification($deposit));
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
