<?php

namespace App\Http\Requests\Clean;

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
}
