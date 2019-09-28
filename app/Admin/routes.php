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
    $router->get('users', 'UsersController@index')->name('admin.users.index');
    $router->get('users/{id}', 'UsersController@show')->name('admin.users.show');
    $router->get('users/{id}/edit', 'UsersController@edit')->name('admin.users.edit');
    $router->put('users/{id}', 'UsersController@update')->name('admin.users.update');
    $router->delete('users/{id}', 'UsersController@destroy')->name('admin.users.destroy');

    /*用户提现*/
    $router->get('user_withdraws', 'UserWithdrawsController@index')->name('admin.user_withdraws');
    $router->post('user_withdraws/{withdraw}/agree', 'UserWithdrawsController@agree')->name('admin.user_withdraws.agree')/*同意*/
    ;
    $router->post('user_withdraws/{withdraw}/deny', 'UserWithdrawsController@deny')->name('admin.user_withdraws.deny')/*拒绝*/
    ;

    /*服务城市*/
    $router->resource('service_sites', 'ServiceSitesController');

    /*回收箱*/
    $router->resource('bins', 'BinsController')->names('admin.bins');

    /*客户端价格*/
    $router->resource('client_prices', 'ClientPricesController');

    /*回收端价格*/
    $router->resource('clean_prices', 'RecyclePricesController');

    /*话题分类*/
    $router->resource('topic_categories', 'TopicCategoriesController');

    /*话题*/
    $router->resource('topics', 'TopicsController');

    /*投递订单*/
    $router->resource('client_orders', 'ClientOrdersController')->names('admin.client_orders');

    /*回收员*/
    $router->get('recyclers/{id}/assignment', 'RecyclersController@assignmentShow')->name('admin.recyclers.assignment.show'); /*分配回收箱 页面*/
    $router->put('recyclers/{id}/assignment', 'RecyclersController@assignmentStore')->name('admin.recyclers.assignment.store'); /*分配回收箱 请求处理*/
    $router->resource('recyclers', 'RecyclersController')->names('admin.recyclers');


    // $router->resource('example', ExampleController::class)->names('admin.example');
    // $router->get('example', 'ExampleController@index')->name('admin.example.index');
    // $router->get('example/create', 'ExampleController@create')->name('admin.example.create');
    // $router->get('example/{id}', 'ExampleController@show')->name('admin.example.show');
    // $router->get('example/{id}/edit', 'ExampleController@edit')->name('admin.example.edit');
    // $router->post('example', 'ExampleController@store')->name('admin.example.store');
    // $router->put('example/{id}', 'ExampleController@update')->name('admin.example.update');
    // $router->delete('example/{id}', 'ExampleController@destroy')->name('admin.example.destroy');
});
