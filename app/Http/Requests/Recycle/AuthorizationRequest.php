<?php

namespace App\Http\Requests\Recycle;
use Illuminate\Foundation\Http\FormRequest;


class AuthorizationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ];
    }
}
