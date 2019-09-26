<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class WechatRequest extends FormRequest
{
    public function rules()
    {
        return [
            'encryptedData' => ['required'],
            'iv' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'encryptedData' => '微信获取encryptedData',
            'iv' => '微信获取iv',
        ];
    }
}
