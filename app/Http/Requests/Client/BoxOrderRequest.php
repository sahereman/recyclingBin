<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class BoxOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'box_no' => ['required', 'exists:boxes,no'],
            'image_proof' => ['required', 'image'],
        ];
    }

    public function attributes()
    {
        return [
            'box_no' => '传统箱编号',
            'image_proof' => '图片凭证',
        ];
    }
}
