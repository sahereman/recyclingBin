<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawWechatPayRequest extends FormRequest
{
    public function rules()
    {
        return [
            'money' => ['required', 'numeric', 'min:0.50', 'max:1000']
        ];
    }

    public function attributes()
    {
        return [
            'money' => '金额',
        ];
    }
}
