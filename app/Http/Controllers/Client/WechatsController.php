<?php

namespace App\Http\Controllers\Client;

use App\Http\Requests\Client\WechatRequest;
use Illuminate\Support\Facades\Auth;

class WechatsController extends Controller
{

    /**
     * showdoc
     * @catalog 客户端/其他相关
     * @title POST 微信数据解密
     * @method POST
     * @url wechats/decryptedData
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param encryptedData 必选 string 微信获取encryptedData
     * @param iv 必选 string 微信获取iv
     * @param cloudID 非必选 string 微信获取cloudID
     * @return {"phoneNumber":"15165360297","purePhoneNumber":"15165360297","countryCode":"86","watermark":{"timestamp":1569488815,"appid":"wx1f30dc232736f812"}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 解密信息
     * @number 10
     */
    public function decryptedData(WechatRequest $request)
    {
        $user = Auth::guard('client')->user();
        $app = app('wechat.mini_program');


        $decryptData = $app->encryptor->decryptData($user['wx_session_key'], $request->input('iv'), $request->input('encryptedData'));


        return $this->response->array($decryptData);
        //        return $this->response->item($topic, new TopicTransformer());
    }

}
