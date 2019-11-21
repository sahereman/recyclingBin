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
    'prefix' => 'api/client',
    'namespace' => 'App\Http\Controllers\Client',
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
        $api->post('authorizations', 'AuthorizationsController@store')->name('client.authorizations.store');/*微信授权token*/
        $api->put('authorizations', 'AuthorizationsController@update')->name('client.authorizations.update');/*刷新授权token*/
        $api->delete('authorizations', 'AuthorizationsController@destroy')->name('client.authorizations.destroy');/*删除授权token*/

        // Banner
        $api->get('banners/{slug}', 'BannersController@index')->name('client.banners.index');/*获取Banner图列表*/

        // 回收箱
        $api->get('bins/nearby', 'BinsController@nearby')->name('client.bins.nearby');/*获取距离最近的回收箱*/
        $api->get('bins', 'BinsController@index')->name('client.bins.index');/*获取回收箱列表*/

        // 传统箱
        $api->get('boxes/nearby', 'BoxesController@nearby')->name('client.boxes.nearby');/*获取距离最近的传统箱*/
        $api->get('boxes/profits', 'BoxesController@profits')->name('client.boxes.profits');/*获取传统箱奖励参数*/
        $api->get('boxes', 'BoxesController@index')->name('client.boxes.index');/*获取传统箱列表*/

        //话题
        $api->get('topic_categories', 'TopicCategoriesController@index')->name('client.topic_categories.index');/*获取话题分类*/
        $api->get('topic_categories/{category}', 'TopicCategoriesController@topic')->name('client.topic_categories.topic');/*获取话题列表*/
        $api->get('topics/{topic}', 'TopicsController@show')->name('client.topics.show');/*获取话题详情*/


        /*需要 token 验证的接口*/
        $api->group(['middleware' => ['api.auth:client', 'client.checkDisabledUser']], function ($api) {


            // 用户
            $api->get('users/show', 'UsersController@show')->name('client.users.show');/*获取用户信息*/
            $api->get('users/notifications', 'UsersController@notifications')->name('client.users.notifications');/*获取消息通知*/
            $api->put('users/real_authentication', 'UsersController@realAuthentication')->name('client.users.real_authentication');/*用户实名认证*/
            $api->post('sms/verification', 'SmsController@verification')->name('client.sms.verification');/*获取短信验证码*/
            $api->put('users/bindPhone', 'UsersController@bindPhone')->name('client.users.bindPhone');/*用户绑定手机*/
            $api->get('users/moneyBill', 'UsersController@moneyBill')->name('client.users.moneyBill');/*获取金钱账单列表*/
            $api->post('users/withdraw/unionPay', 'UsersController@WithdrawUnionPay')->name('client.users.withdraw.unionPay');/*用户银联提现*/


            // 回收箱
            $api->put('bins/qrLogin', 'BinsController@qrLogin')->name('client.bins.qrLogin');/*扫码开箱*/
            $api->get('bins/orderCheck/{token}', 'BinsController@orderCheck')->name('client.bins.orderCheck');/*回收箱订单检查*/

            //投递订单
            $api->get('orders', 'OrdersController@index')->name('client.orders.index');/*获取订单列表*/
            $api->get('orders/{order}', 'OrdersController@show')->name('client.orders.show');/*获取订单详情*/

            //传统箱订单
            $api->get('box_orders', 'BoxOrdersController@index')->name('client.box_orders.index');/*获取传统箱订单列表*/
            $api->get('box_orders/{order}', 'BoxOrdersController@show')->name('client.box_orders.show');/*获取传统箱订单详情*/
            $api->post('box_orders', 'BoxOrdersController@store')->name('client.box_orders.store');/*传统箱投递*/

            //微信
            $api->post('wechats/decryptedData', 'WechatsController@decryptedData')->name('client.wechats.decryptedData');/*微信数据解密*/


        });

        $api->get('test', 'Controller@test')->name('client.test');/*测试*/

        //        // 用户注册
        //        $api->post('users', 'UsersController@store')->name('client.users.store');/*注册*/
        //
        //        // 刷新授权
        //        $api->put('authorizations', 'AuthorizationsController@update')->name('client.authorizations.update');/*刷新授权token*/
        //
        //        //删除授权
        //        $api->delete('authorizations', 'AuthorizationsController@destroy')->name('client.authorizations.destroy');/*删除授权token*/
        //
        //        // 登录
        //        $api->post('authorizations', 'AuthorizationsController@store')->name('client.authorizations.store');/*登录授权token*/
        //
        //        // 文章展示
        //        $api->get('articles/{slug}', 'ArticlesController@show')->name('client.articles.show');/*详情*/
        //
        //        // 城市热门地点
        //        $api->get('city_hot_addresses', 'CityHotAddressesController@index')->name('client.city_hot_addresses.index');/*列表*/
        //
        //        /*需要 token 验证的接口*/
        //        $api->group(['middleware' => 'api.auth:client'], function ($api) {
        //
        //            // 用户
        //            $api->get('users/me', 'UsersController@me')->name('client.users.me');/*获取用户信息*/
        //            $api->patch('users', 'UsersController@update')->name('client.users.update');/*编辑用户信息*/
        //            $api->get('users/to_history', 'UsersController@toHistory')->name('client.users.to_history');/*获取用户目的地历史记录*/
        //
        //            // 订单
        //            $api->get('orders', 'OrdersController@index')->name('client.orders.index');/*获取订单列表*/
        //
        //
        //        });
    });


});
