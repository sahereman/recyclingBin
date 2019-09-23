<?php

namespace App\Handlers;


class SocketJsonHandler
{

    public $array;

    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    /**
     * @return json
     */
    public function toJson()
    {
        return json_encode($this->array, JSON_UNESCAPED_UNICODE);
    }

    public function __toString()
    {
        return (string)$this->toJson();
    }
}