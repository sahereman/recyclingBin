<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class BinsIndexRequest extends FormRequest
{
    public function rules()
    {
        return [
            'lat' => ['required','numeric', 'between:0,90'],
            'lng' => ['required','numeric', 'between:0,180'],
        ];
    }

    public function attributes()
    {
        return [
            'lat' => '纬度',
            'lng' => '经度',
        ];
    }
}
