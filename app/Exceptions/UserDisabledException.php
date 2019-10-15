<?php

namespace App\Exceptions;


use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserDisabledException extends HttpException
{
    /**
     * Create a new unknown version exception instance.
     * @param string $message
     * @param \Exception $previous
     * @param int $code
     * @return void
     */
    public function __construct($message = null, Exception $previous = null, $code = 0)
    {
        parent::__construct(444, $message ?: 'The user is disabled', $previous, [], $code);
    }
}
