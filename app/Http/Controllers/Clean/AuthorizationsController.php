<?php

namespace App\Http\Controllers\Clean;


use App\Http\Requests\Clean\AuthorizationRequest;
use App\Models\Recycler;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthorizationsController extends Controller
{
    /**
     * showdoc
     * @catalog 回收端/回收员相关
     * @title POST 登录授权token
     * @method POST
     * @url authorizations
     * @param username 必选 string 手机号
     * @param password 必选 string 密码
     * @return {"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9iaW4udGVzdFwvYXBpXC9jbGllbnRcL2F1dGhvcml6YXRpb25zIiwiaWF0IjoxNTY3MDQyMDM4LCJleHAiOjE1NjcwNDkxODcsIm5iZiI6MTU2NzA0NTU4NywianRpIjoiY0o1M0ZOY3A0b3pDbmViciIsInN1YiI6MSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.HJCum4zz3djSHek2XjO0szsj_-Aj1_0JloVZ5ozkBUU","token_type":"Bearer","expires_in":3600}
     * @return_param HTTP.Status int 成功时HTTP状态码:201
     * @return_param access_token string 用户凭证
     * @return_param token_type string 凭证类型
     * @return_param expires_in string 有效时间(单位:秒)
     * @remark token的使用规则 : HTTP请求头(Headers) 中加入Authorization的key ,  Value为 : {token_type}(此处有空格){access_token} ------ 到期时间的计算方式 : 现在时间➕有效时间(单位:秒),之后会失效,有效时间内调用刷新授权接口,将换取新的token授权重新计算时间,原有的token将失效
     * @number 10
     */
    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;

        $credentials['phone'] = $username;
        $credentials['password'] = $request->password;

        if (!$token = Auth::guard('clean')->attempt($credentials))
        {
            throw new StoreResourceFailedException(null, [
                'username' => '用户名或密码错误'
            ]);
        }

        $recycler = Auth::guard('clean')->user();

        if ($recycler->disabled_at != null)
        {
            Recycler::recyclerDisabledException();
        }

        return $this->respondWithToken($token)->setStatusCode(201);

    }

    /**
     * showdoc
     * @catalog 回收端/回收员相关
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
        $check = Auth::guard('clean')->parser()->setRequest($request)->hasToken();

        if (!$check)
        {
            throw new TokenInvalidException('Failed to authenticate because of bad credentials or an invalid authorization header.');
        }

        try
        {
            $token = Auth::guard('clean')->refresh();

        } catch (TokenExpiredException $exception)
        {
            // 此处捕获到了 token 过期所抛出的 TokenExpiredException 异常，我们在这里需要做的是刷新该用户的 token 并将它添加到响应头中
            try
            {
                // 刷新用户的 token
                $token = Auth::guard('clean')->refresh();
            } catch (JWTException $exception)
            {
                // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
                throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
            }
        }

        return $this->respondWithToken($token);
    }

    /**
     * showdoc
     * @catalog 回收端/回收员相关
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
        $check = Auth::guard('clean')->parser()->setRequest($request)->hasToken();

        if (!$check)
        {
            throw new TokenInvalidException('Failed to authenticate because of bad credentials or an invalid authorization header.');
        }

        Auth::guard('clean')->logout();
        return $this->response->noContent();
    }


    public function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('clean')->factory()->getTTL() * 60 // token有效的时间(单位:秒)
        ]);
    }

}
