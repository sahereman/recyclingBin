<?php

namespace App\Http\Controllers\Client;

use App\Models\Bin;
use App\Models\ClientOrder;
use App\Models\Recycler;
use App\Models\User;
use App\Notifications\Client\ClientOrderCompletedNotification;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller as BaseController;
use Ramsey\Uuid\Uuid;

class Controller extends BaseController
{
    use Helpers;

    public function test()
    {
        $user = User::find(1);
        $bin = Bin::find(1);
        $order = ClientOrder::find(1);

        $recycler = Recycler::find(1);

//        $type_fabric = $bin->type_fabric()->with(['client_price', 'recycle_price'])->first();
////        $type_fabric = $bin->type_fabric;
//
//        $type_fabric = $bin->type_fabric()->with(['client_price', 'recycle_price'])->first()->toArray();
//        $type_paper = $bin->type_paper()->with(['client_price', 'recycle_price'])->first()->toArray();
//        $a =  ['type_fabric' => $type_fabric, 'type_paper' => $type_paper];


        $user->notify(new ClientOrderCompletedNotification($order));

//        dd(ClientOrder::class);
        dd($order->getMorphClass());
        $a = $bin->recyclers;
        $b = $recycler->bins;
        dd($a,$b);
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
