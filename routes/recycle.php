<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'prefix' => 'api/recycle',
    'namespace' => 'App\Http\Controllers\Recycle',
    'middleware' => ['serializer:array', 'bindings']
], function ($api) {

    /*常规接口调用频率 1分钟 60次*/
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        /*游客可以访问的接口*/


        // 授权
        $api->post('authorizations', 'AuthorizationsController@store')->name('recycle.authorizations.store');/*登录授权token*/
        $api->put('authorizations', 'AuthorizationsController@update')->name('recycle.authorizations.update');/*刷新授权token*/
        $api->delete('authorizations', 'AuthorizationsController@destroy')->name('recycle.authorizations.destroy');/*删除授权token*/

        /*需要 token 验证的接口*/
        $api->group(['middleware' => 'api.auth:recycle'], function ($api) {

            // 回收员
            $api->get('recyclers/show', 'RecyclersController@show')->name('recycle.recyclers.show');/*获取回收员信息*/

            //
            //            // 首页
            //            $api->get('index/stats', 'IndexController@stats')->name('driver.index.stats');/*获取首页统计订单*/
            //
            //            // 司机
            //            $api->get('drivers/me', 'DriversController@me')->name('driver.drivers.me');/*获取司机信息*/
            //
            //            // 订单
            //            $api->get('orders', 'OrdersController@index')->name('driver.orders.index');/*获取订单列表*/

        });
    });


});
