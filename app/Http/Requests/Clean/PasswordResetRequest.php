<?php

namespace App\Http\Requests\Clean;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
{

    public function rules()
    {
        return [
            'phone' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/',
            ],
            'verification_key' => ['required', 'string'],
            'verification_code' => ['required', 'string'],
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function attributes()
    {
        return [
            'phone' => '手机号',
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码',
            'password' => '密码',
        ];
    }
}
