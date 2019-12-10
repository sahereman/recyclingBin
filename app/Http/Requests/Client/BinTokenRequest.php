<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class BinTokenRequest extends FormRequest
{
    public function rules()
    {
        return [
            'token' => ['required', 'min:16', 'exists:bin_tokens,token'],
        ];
    }

    public function attributes()
    {
        return [
            'token' => '二维码',
        ];
    }

    public function messages()
    {
        return [
            'token.exists' => '二维码已失效',
        ];
    }
}
