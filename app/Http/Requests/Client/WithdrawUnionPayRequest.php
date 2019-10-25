<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawUnionPayRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'bank' => ['required'],
            'account' => ['required'],
            'bank_name' => ['required'],
            'money' => ['required', 'numeric','min:5', 'max:1000']
        ];
    }

    public function attributes()
    {
        return [
            'name' => '持卡人姓名',
            'bank' => '银行',
            'account' => '账号',
            'money' => '金额',
            'bank_name' => '开户行',
        ];
    }
}
