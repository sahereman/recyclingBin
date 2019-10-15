<?php

namespace App\Http\Requests\Clean;

use Illuminate\Foundation\Http\FormRequest;

class DepositWechatRequest extends FormRequest
{
    public function rules()
    {
        return [
            'money' => ['required','numeric','min:0.01'],
        ];
    }

    public function attributes()
    {
        return [
            'money' => '金额',
        ];
    }
}
