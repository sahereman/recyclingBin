<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    public function __construct(string $message = "", int $code = 422)
    {
        parent::__construct($message, $code);
    }

    public function render(Request $request)
    {
        if ($request->expectsJson())
        {
            // json() 方法第二个参数就是 Http 返回码
            return response()->json([
                'exception' => ['message' => $this->getMessage()]
            ], $this->code);
        }

        return view('errors.422', [
            'exception' => $this,
        ]);
    }
}
