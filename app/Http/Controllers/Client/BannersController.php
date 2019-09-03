<?php

namespace App\Http\Controllers\Client;

use App\Models\Banner;
use App\Transformers\Client\BannerTransformer;
use Illuminate\Http\Request;

class BannersController extends Controller
{
    /**
     * showdoc
     * @catalog 客户端/Banner图相关
     * @title GET 获取Banner图列表
     * @method GET
     * @url banners/{slug} 小程序首页: mini-index
     * @return {"data":[{"id":1,"slug":"mini-index","image_url":"https://lorempixel.com/640/480/?17153","link":"","sort":9},{"id":3,"slug":"mini-index","image_url":"https://lorempixel.com/640/480/?60560","link":"","sort":18},{"id":2,"slug":"mini-index","image_url":"https://lorempixel.com/640/480/?96377","link":"","sort":82}]}
     * @return_param HTTP.Status int 成功时HTTP状态码:200
     * @return_param data.* json Banner图列表信息
     * @number 10
     */
    public function index($slug)
    {
        $banners = Banner::where('slug',$slug)->orderBy('sort')->get();

        return $this->response->collection($banners, new BannerTransformer());
    }
}
