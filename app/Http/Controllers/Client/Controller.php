<?php

namespace App\Http\Controllers\Client;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Client\UploadImageRequest;
use App\Http\Requests\Request;
use App\Jobs\UserWithdrawWechatPay;
use App\Models\Bin;
use App\Models\ClientOrder;
use App\Models\Recycler;
use App\Models\User;
use App\Models\UserWithdraw;
use App\Notifications\Client\ClientOrderCompletedNotification;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    use Helpers;

    /**
     * showdoc
     * @catalog 客户端/其他相关
     * @title POST 图片上传
     * @method POST
     * @url upload/image
     * @param Headers.Authorization 必选 headers 用户凭证
     * @param file 必选 image 图片文件
     * @return {"path":"original/201911/GOAD7gdOfpJh1FCvJzeazp0AtXCrlCBkUZr1bSK3.jpeg","preview":"http://bin.test/storage/original/201911/GOAD7gdOfpJh1FCvJzeazp0AtXCrlCBkUZr1bSK3.jpeg"}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param * json 图片信息
     * @number 20
     */
    public function uploadImage(UploadImageRequest $request, ImageUploadHandler $uploader)
    {
        $path = $uploader->uploadOriginal($request->file('file'));

        return $this->response->array([
            'path' => $path,
            'preview' => \Storage::disk('public')->url($path),
        ]);
    }

    public function test()
    {
        return '';
//        $w = UserWithdraw::find(22);
//        UserWithdrawWechatPay::dispatchNow($w);
//
//        return $w;

        $box_admin_user = \Encore\Admin\Auth\Database\Role::where('slug', 'box_admin')->first()->administrators->first();

        dd($box_admin_user);
        return 'test';

        $user = User::find(1);
        $bin = Bin::find(9);
        $order = ClientOrder::find(1);

        $recycler = Recycler::find(1);


        //        dump($recycler->bins[0]->pivot);

        $bin = $recycler->bins->where('no', '0532009')->first();

        dump($bin);

        exit();
        //        dd($bin->token->related);
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
        dd($a, $b);
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
