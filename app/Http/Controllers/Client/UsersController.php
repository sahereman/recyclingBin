<?php

namespace App\Http\Controllers\Client;


use App\Http\Requests\Client\BindPhoneRequest;
use App\Transformers\Client\UserTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{

    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title PUT 用户绑定手机
     * @method PUT
     * @url users/bindPhone
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param phone 必选 string 手机号
     * @param verification_key 必选 string 短信验证码key
     * @param verification_code 必选 string 短信验证码
     * @return {"data":{"id":1,"wx_openid":"kZeqcFTG2xgJ0yM9","name":"伍玉兰","gender":"女","phone":"18600982820","avatar_url":"https://lorempixel.com/640/480/?95254","money":"0.00","created_at":"2019-08-24 19:37:26","updated_at":"2019-08-29 09:56:56"}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 用户信息
     * @number 50
     */
    public function show()
    {
        $user = Auth::guard('client')->user();

        return $this->response->item($user, new UserTransformer());
    }

    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title GET 获取用户信息
     * @method GET
     * @url users/show
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"data":{"id":1,"wx_openid":"kZeqcFTG2xgJ0yM9","name":"伍玉兰","gender":"女","phone":"18600982820","avatar_url":"https://lorempixel.com/640/480/?95254","money":"0.00","created_at":"2019-08-24 19:37:26","updated_at":"2019-08-29 09:56:56"}}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 用户信息
     * @number 60
     */
    public function bindPhone(BindPhoneRequest $request)
    {
        $verify_data = Cache::get($request->verification_key);
        // Cache::forget($request->verification_key);// 清除验证码缓存

        if (!$verify_data || !hash_equals($verify_data['code'], $request->verification_code))
        {
            throw new StoreResourceFailedException(null, [
                'verification_code' => '验证码错误'
            ]);
        }

        $user = Auth::guard('client')->user();

        $user->phone = $verify_data['phone'];
        $user->save();

        return $this->response->item($user, new UserTransformer());
    }



    //    public function update(UserRequest $request, ImageUploadHandler $handler)
    //    {
    //        $user = Auth::guard('client')->user();
    //
    //        $attributes = $request->only(['avatar']);
    //
    //        if ($request->avatar)
    //        {
    //            $attributes['avatar'] = $handler->uploadOriginal($request->avatar, 'avatar/' . date('Ym', now()->timestamp), $request->avatar->hashName());
    //        }
    //
    //        $user->update($attributes);
    //
    //        return $this->response->item($user, new UserTransformer());
    //    }
}
