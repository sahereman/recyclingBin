<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller as BaseController;
use Ramsey\Uuid\Uuid;

class Controller extends BaseController
{
    use Helpers;

    public function test()
    {
        return '666';
        //        $swoole = app('swoole');
        //        dd($swoole->stats());

        //        $map = new TencentMapHandler();
        //
        //        return $map->reverseGeocoder(36.092484, 120.380966);

//        return User::$redis_id;
//
//
//        return Uuid::uuid4()->getHex();
//        return '111';
    }
}
