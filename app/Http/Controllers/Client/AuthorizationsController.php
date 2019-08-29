<?php

namespace App\Http\Controllers\Client;


use App\Http\Requests\Client\AuthorizationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthorizationsController extends Controller
{

    public function store(AuthorizationRequest $request)
    {
        $app = app('wechat.mini_program');

        /*测试模式*/
        if (app()->environment('production'))
        {
            $user = User::find($request->user_id);
            $token = Auth::guard('client')->login($user);
            return $this->respondWithToken($token)->setStatusCode(201);
        }

        $wx_session = $app->auth->session($request->input('jsCode'));

        $decryptData = $app->encryptor->decryptData($wx_session['session_key'], $request->input('iv'), $request->input('encryptedData'));

        $user = User::where('wx_openid', $decryptData['openid'])->first();

        if (!$user)
        {
            $user = User::create([
                'wx_openid' => $decryptData['openid'],
                'name' => $decryptData['nickName'],
                'gender' => $decryptData['gender'] == 2 ? '女' : '男',
                'avatar' => $decryptData['avatarUrl'],
                'money' => 0,

                'country' => $decryptData['country'],
                'province' => $decryptData['province'],
                'city' => $decryptData['city'],
            ]);
        }

        $token = Auth::guard('client')->login($user);

        return $this->respondWithToken($token)->setStatusCode(201);
    }


    public function update()
    {
        $token = Auth::guard('client')->refresh();

        return $this->respondWithToken($token);
    }


    public function destroy()
    {
        Auth::guard('client')->logout();
        return $this->response->noContent();
    }


    public function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('client')->factory()->getTTL() * 60 // token有效的时间(单位:秒)
        ]);
    }

}
