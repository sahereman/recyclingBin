<?php

namespace App\Http\Controllers\Client;


use App\Http\Requests\Client\AuthorizationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TheSeer\Tokenizer\TokenCollectionException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthorizationsController extends Controller
{
    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title POST 微信授权token
     * @method POST
     * @url authorizations
     * @param jsCode 必选 string wx.login获取的code
     * @param iv 必选 string wx.getUserInfo获取的iv
     * @param encryptedData 必选 string wx.getUserInfo获取的encryptedData
     * @return {"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9iaW4udGVzdFwvYXBpXC9jbGllbnRcL2F1dGhvcml6YXRpb25zIiwiaWF0IjoxNTY3MDQ0MTQwLCJleHAiOjE1NjcwNDc3NDAsIm5iZiI6MTU2NzA0NDE0MCwianRpIjoiTHV5dWg0Nk1HcEFkcmk5ZiIsInN1YiI6MSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.30XBf3te_QkGcKNDNclWnD18Odn9zCbMikROIanWiBk","token_type":"Bearer","expires_in":3600}
     * @return_param HTTP.Status int 成功时HTTP状态码:201
     * @return_param access_token string 用户凭证
     * @return_param token_type string 凭证类型
     * @return_param expires_in string 有效时间(单位:秒)
     * @remark token的使用规则 : HTTP请求头(Headers) 中加入Authorization的key ,  Value为 : {token_type}(此处有空格){access_token} ------ 到期时间的计算方式 : 现在时间➕有效时间(单位:秒),之后会失效,有效时间内调用刷新授权接口,将换取新的token授权重新计算时间,原有的token将失效
     * @number 10
     */
    public function store(AuthorizationRequest $request)
    {
        $app = app('wechat.mini_program');

        /*测试模式*/
        if (!app()->environment('production'))
        {
            $user = User::find($request->user_id);
            $token = Auth::guard('client')->login($user);
            return $this->respondWithToken($token)->setStatusCode(201);
        }

        $wx_session = $app->auth->session($request->input('jsCode'));

        $decryptData = $app->encryptor->decryptData($wx_session['session_key'], $request->input('iv'), $request->input('encryptedData'));

        $user = User::where('wx_openid', $decryptData['openId'])->first();
        if($user->disabled_at != null)
        {
            $this->response->errorForbidden();
        }
        //        info($decryptData);

        if (!$user)
        {
            $user = User::create([
                'wx_openid' => $decryptData['openId'],
                'wx_session_key' => $wx_session['session_key'],
                'name' => $decryptData['nickName'],
                'gender' => $decryptData['gender'] == 2 ? '女' : '男',
                'avatar' => $decryptData['avatarUrl'],
                'money' => 0,

                'wx_country' => $decryptData['country'],
                'wx_province' => $decryptData['province'],
                'wx_city' => $decryptData['city'],
            ]);
        } else
        {
            $user->update([
                'wx_session_key' => $wx_session['session_key'],
                'name' => $decryptData['nickName'],
                'gender' => $decryptData['gender'] == 2 ? '女' : '男',
                'avatar' => $decryptData['avatarUrl'],
                'wx_country' => $decryptData['country'],
                'wx_province' => $decryptData['province'],
                'wx_city' => $decryptData['city'],
            ]);
        }


        $token = Auth::guard('client')->login($user);

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title PUT 刷新授权token
     * @method PUT
     * @url authorizations
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9iaW4udGVzdFwvYXBpXC9jbGllbnRcL2F1dGhvcml6YXRpb25zIiwiaWF0IjoxNTY3MDQyMDM4LCJleHAiOjE1NjcwNDkxODcsIm5iZiI6MTU2NzA0NTU4NywianRpIjoiY0o1M0ZOY3A0b3pDbmViciIsInN1YiI6MSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.HJCum4zz3djSHek2XjO0szsj_-Aj1_0JloVZ5ozkBUU","token_type":"Bearer","expires_in":3600}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param access_token string 用户凭证
     * @return_param token_type string 凭证类型
     * @return_param expires_in string 有效时间(单位:秒)
     * @remark token的使用规则 : HTTP请求头(Headers) 中加入Authorization的key ,  Value为 : {token_type}(此处有空格){access_token} ------ 到期时间的计算方式 : 现在时间➕有效时间(单位:秒),之后会失效,有效时间内调用刷新授权接口,将换取新的token授权重新计算时间,原有的token将失效
     * @number 20
     * @throws TokenInvalidException
     */
    public function update(Request $request)
    {
        $check = Auth::guard('client')->parser()->setRequest($request)->hasToken();

        if (!$check)
        {
            throw new TokenInvalidException('Failed to authenticate because of bad credentials or an invalid authorization header.');
        }

        try
        {
            $token = Auth::guard('client')->refresh();

        } catch (TokenExpiredException $exception)
        {
            // 此处捕获到了 token 过期所抛出的 TokenExpiredException 异常，我们在这里需要做的是刷新该用户的 token 并将它添加到响应头中
            try
            {
                // 刷新用户的 token
                $token = Auth::guard('client')->refresh();
            } catch (JWTException $exception)
            {
                // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
                throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
            }
        }


        $user = Auth::guard('client')->setToken($token)->user();
        if($user->disabled_at != null)
        {
            $this->response->errorForbidden();
        }

        return $this->respondWithToken($token);
    }

    /**
     * showdoc
     * @catalog 客户端/用户相关
     * @title PUT 删除授权token
     * @method DELETE
     * @url authorizations
     * @param Headers.Authorization 必选 headers 用户凭证
     * @return {"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9iaW4udGVzdFwvYXBpXC9jbGllbnRcL2F1dGhvcml6YXRpb25zIiwiaWF0IjoxNTY3MDQyMDM4LCJleHAiOjE1NjcwNDkxODcsIm5iZiI6MTU2NzA0NTU4NywianRpIjoiY0o1M0ZOY3A0b3pDbmViciIsInN1YiI6MSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.HJCum4zz3djSHek2XjO0szsj_-Aj1_0JloVZ5ozkBUU","token_type":"Bearer","expires_in":3600}
     * @return_param HTTP.Status int 成功时HTTP状态码:204
     * @number 30
     * @throws TokenInvalidException
     */
    public function destroy(Request $request)
    {
        $check = Auth::guard('client')->parser()->setRequest($request)->hasToken();

        if (!$check)
        {
            throw new TokenInvalidException('Failed to authenticate because of bad credentials or an invalid authorization header.');
        }

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
