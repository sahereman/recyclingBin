<?php

namespace App\Http\Controllers\Clean;


use App\Http\Requests\Clean\DepositWechatRequest;
use App\Models\RecyclerDeposit;
use App\Models\RecyclerPayment;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\Auth;


class DepositsController extends Controller
{

    /**
     * showdoc
     * @catalog 回收端/回收员相关
     * @title POST 回收员充值
     * @method POST
     * @url deposits/wechat
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param money 必选 string 充值金额
     * @return {"wx_pay":{"appId":"wx1f30dc232736f812","nonceStr":"5da433231639f","package":"prepay_id=wx14163443090246d0c5b5fc3f1611775400","signType":"MD5","paySign":"E1ADC9E5D9A959EC10F04699F7C6256F","timestamp":"1571042083"}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param wx_pay.* json 客户端发起微信支付所需参数
     * @number 35
     */
    public function wechat(DepositWechatRequest $request)
    {
        $recycler = Auth::guard('clean')->user();


        if (!$recycler || $recycler->wx_openid == null)
        {
            throw new StoreResourceFailedException(null, [
                'money' => '用户未微信授权'
            ]);
        }

        $payment = new RecyclerPayment();
        $payment->recycler()->associate($recycler);
        $payment->amount = $request->money;
        $payment->method = RecyclerPayment::METHOD_WECHAT;
        $payment->related_model = RecyclerDeposit::class;
        $payment->save();


        $deposit = new RecyclerDeposit();
        $deposit->status = RecyclerDeposit::STATUS_PAYING;
        $deposit->money = $request->money;
        $deposit->recycler()->associate($recycler);
        $deposit->payment()->associate($payment);
        $deposit->save();

        $app = app('wechat.payment.clean');

        $result = $app->order->unify([
            'body' => '小黑点回收员充值',
            'out_trade_no' => $payment->sn,
            'total_fee' => bcmul($payment->amount, 100),
            'notify_url' => app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('clean.payments.wechatNotify'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI',
            'openid' => $recycler->wx_openid,
        ]);

        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS')
        {
            $prepayId = $result['prepay_id'];
            $config = $app->jssdk->sdkConfig($prepayId); // 返回数组

            return $this->response->array([
                'wx_pay' => $config,
            ]);

        } else
        {
            throw new StoreResourceFailedException(null, [
                'money' => '微信支付失败,请重试'
            ]);
        }
    }


}
