<?php

namespace App\Http\Controllers\Client;


use App\Http\Requests\Client\BindPhoneRequest;
use App\Transformers\Client\UserTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{

    public function show()
    {
        $user = Auth::guard('client')->user();

        return $this->response->item($user, new UserTransformer());
    }

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
