<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Database\Seeder;

/**
 * Class AdminTablesSeeder
 */
class AdminTablesSeeder extends Seeder
{
    /*自定义添加的权限*/
    private $custom_permissions =
        [
            [
                'name' => '用户管理',
                'slug' => 'users',
                'http_method' => '',
                'http_path' => "/users",
            ],
            [
                'name' => '回收站点管理',
                'slug' => 'service_sites',
                'http_method' => '',
                'http_path' => "/service_sites",
            ],
            [
                'name' => '回收垃圾桶管理',
                'slug' => 'bins',
                'http_method' => '',
                'http_path' => "/bins",
            ],
            [
                'name' => '客户端价格管理',
                'slug' => 'client_prices',
                'http_method' => '',
                'http_path' => "/client_prices",
            ],
            [
                'name' => '回收端价格管理',
                'slug' => 'clean_prices',
                'http_method' => '',
                'http_path' => "/clean_prices",
            ],
            [
                'name' => '话题分类管理',
                'slug' => 'topic_categories',
                'http_method' => '',
                'http_path' => "/topic_categories",
            ],
            [
                'name' => '话题管理',
                'slug' => 'topics',
                'http_method' => '',
                'http_path' => "/topics",
            ],
            [
                'name' => '订单管理',
                'slug' => 'client_orders',
                'http_method' => '',
                'http_path' => "/client_orders",
            ],
        ];

    /*自定义添加的菜单*/
    private $custom_menus =
        [
            // 菜单组 ID:14 作为起始数据
            [
                'parent_id' => 0,
                'order' => 20,
                'title' => '用户管理',
                'icon' => 'fa-users',
                'uri' => '',
            ],
            [
                'parent_id' => 0,
                'order' => 30,
                'title' => '回收员管理',
                'icon' => 'fa-users',
                'uri' => '',
            ],
            [
                'parent_id' => 0,
                'order' => 40,
                'title' => '回收箱管理',
                'icon' => 'fa-recycle',
                'uri' => '',
            ],
            [
                'parent_id' => 0,
                'order' => 50,
                'title' => '话题管理',
                'icon' => 'fa-archive',
                'uri' => '',
            ],


            //用户管理
            [
                'parent_id' => 14,
                'order' => 2,
                'title' => '用户列表',
                'icon' => 'fa-users',
                'uri' => 'users',
            ],
            [
                'parent_id' => 14,
                'order' => 3,
                'title' => '用户提现申请',
                'icon' => 'fa-users',
                'uri' => 'user_withdraws',
            ],
            [
                'parent_id' => 14,
                'order' => 5,
                'title' => '投递订单列表',
                'icon' => 'fa-list',
                'uri' => 'client_orders',
            ],

            //回收员管理
            [
                'parent_id' => 15,
                'order' => 2,
                'title' => '回收员列表',
                'icon' => 'fa-users',
                'uri' => 'recyclers',
            ],
            [
                'parent_id' => 15,
                'order' => 3,
                'title' => '回收员提现申请',
                'icon' => 'fa-users',
                'uri' => 'recycler_withdraws',
            ],
            [
                'parent_id' => 15,
                'order' => 5,
                'title' => '回收订单列表',
                'icon' => 'fa-list',
                'uri' => 'clean_orders',
            ],

            //回收箱管理
            [
                'parent_id' => 16,
                'order' => 10,
                'title' => '回收箱列表',
                'icon' => 'fa-bitbucket',
                'uri' => 'bins',
            ],
            [
                'parent_id' => 16,
                'order' => 20,
                'title' => '客户端价格',
                'icon' => 'fa-yen',
                'uri' => 'client_prices',
            ],
            [
                'parent_id' => 16,
                'order' => 30,
                'title' => '回收端价格',
                'icon' => 'fa-dollar',
                'uri' => 'clean_prices',
            ],
            [
                'parent_id' => 16,
                'order' => 40,
                'title' => '服务城市列表',
                'icon' => 'fa-sitemap',
                'uri' => 'service_sites',
            ],


            //话题管理
            [
                'parent_id' => 17,
                'order' => 5,
                'title' => '话题分类',
                'icon' => 'fa-list-ol',
                'uri' => 'topic_categories',
            ],
            [
                'parent_id' => 17,
                'order' => 10,
                'title' => '话题列表',
                'icon' => 'fa-file-text',
                'uri' => 'topics',
            ],

        ];

    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        // create a user.
        Administrator::truncate();
        Administrator::create([
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'name' => 'Admin',
        ]);

        // create a role.
        Role::truncate();
        Role::create([
            'name' => '超级管理员',
            'slug' => 'administrator',
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::first());

        //create a permission
        Permission::truncate();
        $permissions = [
            [
                'name' => '所有权限',
                'slug' => '*',
                'http_method' => '',
                'http_path' => '*',
            ],
            [
                'name' => '首页',
                'slug' => 'index',
                'http_method' => 'GET',
                'http_path' => '/',
            ],
            [
                'name' => '登录',
                'slug' => 'auth.login',
                'http_method' => '',
                'http_path' => "/auth/login\r\n/auth/logout",
            ],
            [
                'name' => '个人设置',
                'slug' => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path' => '/auth/setting',
            ],
            [
                'name' => '系统管理',
                'slug' => 'auth.management',
                'http_method' => '',
                'http_path' => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs\r\n/media*\r\n/logs*\r\n/dashboard\r\n/redis\r\n/horizon",
            ],
            [
                'name' => '系统设置',
                'slug' => 'configs',
                'http_method' => '',
                'http_path' => "/configs",
            ],
        ];
        $permissions = array_merge($permissions, $this->custom_permissions);
        Permission::insert($permissions);

        Role::first()->permissions()->save(Permission::first());

        // add default menus.
        Menu::truncate();
        $menus = [
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => '首页',
                'icon' => 'fa-bar-chart',
                'uri' => '/',
            ],
            [
                'parent_id' => 0,
                'order' => 999,
                'title' => '系统管理',
                'icon' => 'fa-tasks',
                'uri' => '',
            ],
            [
                'parent_id' => 2,
                'order' => 2,
                'title' => '系统信息',
                'icon' => 'fa-dashboard',
                'uri' => 'dashboard',
            ],
            [
                'parent_id' => 2,
                'order' => 3,
                'title' => '管理员',
                'icon' => 'fa-users',
                'uri' => 'auth/users',
            ],
            [
                'parent_id' => 2,
                'order' => 4,
                'title' => '角色',
                'icon' => 'fa-user',
                'uri' => 'auth/roles',
            ],
            [
                'parent_id' => 2,
                'order' => 5,
                'title' => '权限',
                'icon' => 'fa-ban',
                'uri' => 'auth/permissions',
            ],
            [
                'parent_id' => 2,
                'order' => 6,
                'title' => '菜单',
                'icon' => 'fa-bars',
                'uri' => 'auth/menu',
            ],
            [
                'parent_id' => 2,
                'order' => 7,
                'title' => '文件管理',
                'icon' => 'fa-file',
                'uri' => 'media',
            ],
            [
                'parent_id' => 2,
                'order' => 7,
                'title' => '系统日志',
                'icon' => 'fa-database',
                'uri' => 'logs',
            ],
            [
                'parent_id' => 2,
                'order' => 8,
                'title' => '操作日志',
                'icon' => 'fa-history',
                'uri' => 'auth/logs',
            ],
            [
                'parent_id' => 2,
                'order' => 9,
                'title' => 'Horizon',
                'icon' => 'fa-desktop',
                'uri' => 'horizon',
            ],
            [
                'parent_id' => 2,
                'order' => 10,
                'title' => 'Redis',
                'icon' => 'fa-database',
                'uri' => 'redis',
            ],
            [
                'parent_id' => 0,
                'order' => 998,
                'title' => '系统设置',
                'icon' => 'fa-gear',
                'uri' => 'configs',
            ],
        ];
        $menus = array_merge($menus, $this->custom_menus);
        Menu::insert($menus);

        // add role to menu.
        Menu::find(2)->roles()->save(Role::first());
        Menu::find(15)->roles()->save(Role::first());/*文章管理*/
    }
}
