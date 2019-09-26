<?php

namespace App\Http\Controllers\Client;

use App\Http\Requests\Client\WechatRequest;
use App\Models\Topic;
use App\Transformers\Client\TopicTransformer;
use Illuminate\Support\Facades\Auth;

class WechatsController extends Controller
{

    /**
     * showdoc
     * @catalog 客户端/其他相关
     * @title GET 微信数据解密
     * @method GET
     * @url decryptedData
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @number 10
     */
    public function decryptedData(WechatRequest $request)
    {
        $user = Auth::guard('client')->user();
        $app = app('wechat.mini_program');

        $decryptData = $app->encryptor->decryptData($user['session_key'], $request->input('iv'), $request->input('encryptedData'));


        return $this->response->array($decryptData);
        //        return $this->response->item($topic, new TopicTransformer());
    }

}
