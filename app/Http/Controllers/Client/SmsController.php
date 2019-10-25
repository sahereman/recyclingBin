<?php

namespace App\Http\Controllers\Client;

use App\Http\Requests\Client\SmsVerificationRequest;
use App\Jobs\SendSms;

class SmsController extends Controller
{
    public $verification_template_code = 'SMS_175543046';

    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title POST 发送短信验证码
     * @method POST
     * @url sms/verification
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param phone 必选 string 手机号
     * @return {"verification_key":"SmsVerification_tGH5UZhHnlb9rAn","expired_at":"2019-08-29 11:03:54"}
     * @return_param HTTP.Status int 成功时HTTP状态码:201
     * @return_param verification_key string 短信验证码key
     * @return_param expired_at string 短信验证码过期时间
     * @number 40
     * @throws \Exception
     */
    public function verification(SmsVerificationRequest $request)
    {
        $phone = $request->phone;

        if (!app()->environment('production'))
        {
            $code = '1234';
        } else
        {
            // 生成4位随机数，左侧补0
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

            $content = "您的验证码为：{$code}，该验证码 5 分钟内有效，请勿泄漏于他人。";

            SendSms::dispatch($phone, $this->verification_template_code, $content, [
                'code' => $code
            ]);
        }

        $key = 'SmsVerification_' . str_random(15);
        $expiredAt = now()->addMinutes(10);
        // 缓存验证码 10分钟过期。
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'verification_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
