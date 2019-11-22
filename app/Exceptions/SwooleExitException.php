<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class SwooleExitException extends Exception
{
    protected $response;

    public function __construct($response, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    //获取响应内容
    public function getResponse()
    {
        return $this->response;
    }
}