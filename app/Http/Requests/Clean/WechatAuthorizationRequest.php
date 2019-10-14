<?php

namespace App\Http\Requests\Clean;
use Illuminate\Foundation\Http\FormRequest;

class WechatAuthorizationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'jsCode' => ['required'],
            'iv' => ['required'],
            'encryptedData' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'jsCode' => 'wx.login 获取的code',
            'iv' => 'wx.getUserInfo 获取的iv',
            'encryptedData' => 'wx.getUserInfo 获取的encryptedData',
        ];
    }
}
