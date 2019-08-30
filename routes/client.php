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
        $api->post('authorizations', 'AuthorizationsController@store')->name('client.authorizations.store');/*小程序授权token*/


        /*需要 token 验证的接口*/
        $api->group(['middleware' => 'api.auth:client'], function ($api) {

            //授权
            $api->put('authorizations', 'AuthorizationsController@update')->name('client.authorizations.update');/*刷新授权token*/
            $api->delete('authorizations', 'AuthorizationsController@destroy')->name('client.authorizations.destroy');/*删除授权token*/


            // 用户
            $api->get('users/show', 'UsersController@show')->name('client.users.show');/*获取用户信息*/
            $api->post('sms/verification', 'SmsController@verification')->name('client.sms.verification');/*获取短信验证码*/
            $api->put('users/bindPhone', 'UsersController@bindPhone')->name('client.users.bindPhone');/*用户绑定手机*/

            //回收箱
            $api->get('bins', 'BinsController@index')->name('client.bins.index');/*获取回收箱列表*/

            //话题
            $api->get('topic_categories', 'TopicCategoriesController@index')->name('client.topic_categories.index');/*获取话题分类*/
            $api->get('topics', 'TopicsController@index')->name('client.topics.index');/*获取话题列表*/
            $api->get('topics/show', 'TopicsController@show')->name('client.topics.show');/*获取话题详情*/



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
