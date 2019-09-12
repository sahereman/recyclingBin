<?php

namespace App\Http\Requests\Clean;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawUnionPayRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'bank' => ['required'],
            'account' => ['required'],
            'money' => ['required', 'integer','min:10', 'max:5000']
        ];
    }

    public function attributes()
    {
        return [
            'name' => '持卡人姓名',
            'bank' => '银行',
            'account' => '账号',
            'money' => '金额',
        ];
    }
}
