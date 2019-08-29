<?php

namespace App\Http\Controllers\Client;

use App\Http\Requests\Client\SmsVerificationRequest;
use App\Jobs\SendSms;
use Illuminate\Support\Facades\Log;
use Overtrue\EasySms\EasySms;

class SmsController extends Controller
{
    public $verification_template_code = 'SMS_151996714';


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
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
