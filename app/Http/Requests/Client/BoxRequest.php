<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class BoxRequest extends FormRequest
{
    public function rules()
    {
        return [
            'lat' => ['required','numeric', 'between:-90,90'],
            'lng' => ['required','numeric', 'between:-180,180'],
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
