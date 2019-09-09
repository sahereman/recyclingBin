<?php

namespace App\Http\Requests\Client;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserRequest extends FormRequest
{

    public function rules()
    {
        if ($this->routeIs('client.users.real_authentication'))
        {
            return [
                'real_name' => 'required|string',
                'real_id' => 'required|string|size:18',
            ];
        } elseif ($this->routeIs('client.users.update'))
        {
            return [
                'name' => [
                    'string', 'max:255',
                    Rule::unique('users')->ignore(Auth::guard('api')->id())
                ],
                'email' => 'email',
                'avatar' => 'image',
            ];
        } else
        {
            throw new NotFoundHttpException();
        }
    }

    public function attributes()
    {
        return [
            'real_name' => '真实姓名',
            'real_id' => '身份证号',
            'name' => '用户名',
            'email' => '邮箱',
            'avatar' => '头像',
            'password' => '密码',
        ];
    }
}