<?php

use Encore\Admin\Facades\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'PagesController@index')->name('admin.root');/*首页 - 数据统计*/
    $router->post('wang_editor/images', 'WangEditorController@images')->name('admin.wang_editor.images');/*WangEditor上传图片*/
    $router->get('dashboard', 'PagesController@dashboard')->name('admin.dashboard');/*系统信息*/
    $router->get('horizon', 'PagesController@horizon')->name('admin.horizon');/*Horizon*/

    /*系统设置*/
    $router->get('configs', 'ConfigsController@index')->name('admin.configs.index');/*详情*/
    $router->post('configs/submit', 'ConfigsController@submit')->name('admin.configs.submit');/*提交*/

    /*用户*/
    $router->get('users/send_message/{id?}', 'UsersController@sendMessageShow')->name('admin.users.send_message.show'); /*群发站内信 页面*/
    $router->post('users/send_message', 'UsersController@sendMessageStore')->name('admin.users.send_message.store'); /*群发站内信 请求处理*/
    $router->get('users', 'UsersController@index')->name('admin.users.index');
    $router->get('users/{id}', 'UsersController@show')->name('admin.users.show');
    $router->get('users/{id}/edit', 'UsersController@edit')->name('admin.users.edit');
    $router->put('users/{id}', 'UsersController@update')->name('admin.users.update');
    $router->delete('users/{id}', 'UsersController@destroy')->name('admin.users.destroy');
    $router->post('users/{user}/disable', 'UsersController@disable')->name('admin.users.disable');/*加入黑名单*/
    $router->post('users/{user}/enable', 'UsersController@enable')->name('admin.users.enable');/*移除黑名单*/

    /*用户提现*/
    $router->get('user_withdraws', 'UserWithdrawsController@index')->name('admin.user_withdraws');
    $router->post('user_withdraws/{withdraw}/agree', 'UserWithdrawsController@agree')->name('admin.user_withdraws.agree');/*同意*/
    $router->post('user_withdraws/{withdraw}/deny', 'UserWithdrawsController@deny')->name('admin.user_withdraws.deny');/*拒绝*/

    /*服务城市*/
    $router->resource('service_sites', 'ServiceSitesController');

    /*Banner*/
    $router->resource('banners', 'BannersController')->names('admin.banners');

    /*回收箱*/
    $router->resource('bins', 'BinsController')->names('admin.bins');

    /*回收箱权限*/
    $router->resource('bin_recyclers', 'BinRecyclersController')->names('admin.bin_recyclers');

    /*客户端价格*/
    $router->resource('client_prices', 'ClientPricesController');

    /*回收端价格*/
    $router->resource('clean_prices', 'CleanPricesController');

    /*话题分类*/
    $router->resource('topic_categories', 'TopicCategoriesController');

    /*话题*/
    $router->resource('topics', 'TopicsController');

    /*投递订单*/
    $router->resource('client_orders', 'ClientOrdersController')->names('admin.client_orders');

    /*回收订单*/
    $router->resource('clean_orders', 'CleanOrdersController')->names('admin.clean_orders');

    /*回收员*/
    $router->get('recyclers/{id}/assignment', 'RecyclersController@assignmentShow')->name('admin.recyclers.assignment.show'); /*分配回收箱 页面*/
    $router->put('recyclers/{id}/assignment', 'RecyclersController@assignmentStore')->name('admin.recyclers.assignment.store'); /*分配回收箱 请求处理*/
    $router->get('recyclers/send_message/{id?}', 'RecyclersController@sendMessageShow')->name('admin.recyclers.send_message.show'); /*群发站内信 页面*/
    $router->post('recyclers/send_message', 'RecyclersController@sendMessageStore')->name('admin.recyclers.send_message.store'); /*群发站内信 请求处理*/
    $router->post('recyclers/{recycler}/disable', 'RecyclersController@disable')->name('admin.recyclers.disable');/*加入黑名单*/
    $router->post('recyclers/{recycler}/enable', 'RecyclersController@enable')->name('admin.recyclers.enable');/*移除黑名单*/
    $router->resource('recyclers', 'RecyclersController')->names('admin.recyclers');

    /*回收员提现*/
    $router->get('recycler_withdraws', 'RecyclerWithdrawsController@index')->name('admin.recycler_withdraws');
    $router->post('recycler_withdraws/{withdraw}/agree', 'RecyclerWithdrawsController@agree')->name('admin.recycler_withdraws.agree');/*同意*/
    $router->post('recycler_withdraws/{withdraw}/deny', 'RecyclerWithdrawsController@deny')->name('admin.recycler_withdraws.deny');/*拒绝*/

    /*传统箱*/
    $router->resource('boxes', 'BoxesController')->names('admin.boxes');

    /*传统订单*/
    $router->post('box_order_check/{order}/agree', 'BoxOrdersController@agree')->name('admin.box_orders.agree'); /*传统订单 审核通过*/
    $router->post('box_order_check/{order}/deny', 'BoxOrdersController@deny')->name('admin.box_orders.deny'); /*传统订单 审核拒绝*/
    $router->resource('box_orders', 'BoxOrdersController')->names('admin.box_orders');

    /*传统箱管理员*/
    $router->resource('box_admin_users', 'BoxAdminUsersController')->names('admin.box_admin_users');
    $router->get('box_admin_users/{id}/assignment', 'BoxAdminUsersController@assignmentShow')->name('admin.box_admin_users.assignment.show'); /*分配传统箱 页面*/
    $router->put('box_admin_users/{id}/assignment', 'BoxAdminUsersController@assignmentStore')->name('admin.box_admin_users.assignment.store'); /*分配传统箱 请求处理*/



    // $router->resource('example', ExampleController::class)->names('admin.example');
    // $router->get('example', 'ExampleController@index')->name('admin.example.index');
    // $router->get('example/create', 'ExampleController@create')->name('admin.example.create');
    // $router->get('example/{id}', 'ExampleController@show')->name('admin.example.show');
    // $router->get('example/{id}/edit', 'ExampleController@edit')->name('admin.example.edit');
    // $router->post('example', 'ExampleController@store')->name('admin.example.store');
    // $router->put('example/{id}', 'ExampleController@update')->name('admin.example.update');
    // $router->delete('example/{id}', 'ExampleController@destroy')->name('admin.example.destroy');
});
