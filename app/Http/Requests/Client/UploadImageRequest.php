<?php

namespace App\Http\Requests\Client;
use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'file' => 'required|image|mimes:jpeg,png,gif',
        ];
    }

    public function attributes()
    {
        return [
            'file' => '图片',
        ];
    }
}
