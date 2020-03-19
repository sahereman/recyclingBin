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
                'http_path' => "/users*",
            ],
            [
                'name' => '回收员管理',
                'slug' => 'recyclers',
                'http_method' => '',
                'http_path' => "/recyclers*",
            ],
            [
                'name' => '回收站点管理',
                'slug' => 'service_sites',
                'http_method' => '',
                'http_path' => "/service_sites*",
            ],
            [
                'name' => '回收箱管理',
                'slug' => 'bins',
                'http_method' => '',
                'http_path' => "/bins*",
            ],
            [
                'name' => '客户端价格管理',
                'slug' => 'client_prices',
                'http_method' => '',
                'http_path' => "/client_prices*",
            ],
            [
                'name' => '回收端价格管理',
                'slug' => 'clean_prices',
                'http_method' => '',
                'http_path' => "/clean_prices*",
            ],
            [
                'name' => '话题分类管理',
                'slug' => 'topic_categories',
                'http_method' => '',
                'http_path' => "/topic_categories*",
            ],
            [
                'name' => '话题管理',
                'slug' => 'topics',
                'http_method' => '',
                'http_path' => "/topics*",
            ],
            [
                'name' => '投递订单管理',
                'slug' => 'client_orders',
                'http_method' => '',
                'http_path' => "/client_orders*",
            ],
            [
                'name' => '回收订单管理',
                'slug' => 'clean_orders',
                'http_method' => '',
                'http_path' => "/clean_orders*",
            ],
            [
                'name' => '传统箱管理',
                'slug' => 'boxes',
                'http_method' => '',
                'http_path' => "/boxes*",
            ],
            [
                'name' => '传统箱订单管理',
                'slug' => 'box_orders',
                'http_method' => '',
                'http_path' => "/box_orders*",
            ],
            [
                'name' => '传统箱订单审核',
                'slug' => 'box_orders.check',
                'http_method' => '',
                'http_path' => "/box_order_check*",
            ],
            [
                'name' => '传统箱管理员管理',
                'slug' => 'box_admin_users',
                'http_method' => '',
                'http_path' => "/box_admin_users*",
            ],
            [
                'name' => 'Banner管理',
                'slug' => 'banners',
                'http_method' => '',
                'http_path' => "/banners*",
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
                'permission' => 'users'
            ],
            [
                'parent_id' => 0,
                'order' => 30,
                'title' => '回收员管理',
                'icon' => 'fa-users',
                'uri' => '',
                'permission' => 'recyclers',
            ],
            [
                'parent_id' => 0,
                'order' => 40,
                'title' => '回收箱管理',
                'icon' => 'fa-recycle',
                'uri' => '',
                'permission' => 'bins',
            ],
            [
                'parent_id' => 0,
                'order' => 45,
                'title' => '传统箱管理',
                'icon' => 'fa-recycle',
                'uri' => '',
                'permission' => 'boxes',
            ],
            [
                'parent_id' => 0,
                'order' => 50,
                'title' => '话题管理',
                'icon' => 'fa-archive',
                'uri' => '',
                'permission' => 'topics',
            ],
            [
                'parent_id' => 0,
                'order' => 55,
                'title' => '回收站点',
                'icon' => 'fa-sitemap',
                'uri' => 'service_sites',
                'permission' => 'service_sites',
            ],
            [
                'parent_id' => 0,
                'order' => 60,
                'title' => 'Banner管理',
                'icon' => 'fa-image',
                'uri' => 'banners',
                'permission' => 'banners',
            ],


            //用户管理
            [
                'parent_id' => 14,
                'order' => 2,
                'title' => '用户列表',
                'icon' => 'fa-users',
                'uri' => 'users',
                'permission' => null,
            ],
            [
                'parent_id' => 14,
                'order' => 3,
                'title' => '用户提现申请',
                'icon' => 'fa-users',
                'uri' => 'user_withdraws',
                'permission' => null,
            ],
            [
                'parent_id' => 14,
                'order' => 4,
                'title' => '投递订单列表',
                'icon' => 'fa-list',
                'uri' => 'client_orders',
                'permission' => null,
            ],
            [
                'parent_id' => 14,
                'order' => 5,
                'title' => '群发站内信',
                'icon' => 'fa-file-text',
                'uri' => 'users/send_message',
                'permission' => null,
            ],

            //回收员管理
            [
                'parent_id' => 15,
                'order' => 2,
                'title' => '回收员列表',
                'icon' => 'fa-users',
                'uri' => 'recyclers',
                'permission' => null,
            ],
            [
                'parent_id' => 15,
                'order' => 3,
                'title' => '回收员提现申请',
                'icon' => 'fa-users',
                'uri' => 'recycler_withdraws',
                'permission' => null,
            ],
            [
                'parent_id' => 15,
                'order' => 4,
                'title' => '回收订单列表',
                'icon' => 'fa-list',
                'uri' => 'clean_orders',
                'permission' => null,
            ],
            [
                'parent_id' => 15,
                'order' => 5,
                'title' => '群发站内信',
                'icon' => 'fa-file-text',
                'uri' => 'recyclers/send_message',
                'permission' => null,
            ],

            //回收箱管理
            [
                'parent_id' => 16,
                'order' => 10,
                'title' => '回收箱列表',
                'icon' => 'fa-bitbucket',
                'uri' => 'bins',
                'permission' => null,
            ],
            [
                'parent_id' => 16,
                'order' => 15,
                'title' => '重量异常警告',
                'icon' => 'fa-bitbucket',
                'uri' => 'bin_weight_warnings',
                'permission' => null,
            ],
            [
                'parent_id' => 16,
                'order' => 20,
                'title' => '客户端价格',
                'icon' => 'fa-yen',
                'uri' => 'client_prices',
                'permission' => null,
            ],
            [
                'parent_id' => 16,
                'order' => 30,
                'title' => '回收端价格',
                'icon' => 'fa-dollar',
                'uri' => 'clean_prices',
                'permission' => null,
            ],

            //传统箱管理
            [
                'parent_id' => 17,
                'order' => 5,
                'title' => '传统箱列表',
                'icon' => 'fa-list-ol',
                'uri' => 'boxes',
                'permission' => null,
            ],
            [
                'parent_id' => 17,
                'order' => 8,
                'title' => '传统箱订单列表',
                'icon' => 'fa-list-ol',
                'uri' => 'box_orders',
                'permission' => null,
            ],
            [
                'parent_id' => 17,
                'order' => 10,
                'title' => '传统箱管理员列表',
                'icon' => 'fa-list-ol',
                'uri' => 'box_admin_users',
                'permission' => 'box_admin_users',
            ],

            //话题管理
            [
                'parent_id' => 18,
                'order' => 5,
                'title' => '话题分类',
                'icon' => 'fa-list-ol',
                'uri' => 'topic_categories',
                'permission' => null,
            ],
            [
                'parent_id' => 18,
                'order' => 10,
                'title' => '话题列表',
                'icon' => 'fa-file-text',
                'uri' => 'topics',
                'permission' => null,
            ],


        ];

    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        Administrator::truncate();
        Role::truncate();
        Menu::truncate();
        Permission::truncate();
        DB::table(config('admin.database.operation_log_table'))->truncate();
        DB::table(config('admin.database.user_permissions_table'))->truncate();
        DB::table(config('admin.database.role_users_table'))->truncate();
        DB::table(config('admin.database.role_permissions_table'))->truncate();
        DB::table(config('admin.database.role_menu_table'))->truncate();

        // create a user.
        Administrator::create([
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'name' => 'Admin',
        ]);

        Administrator::create([
            'username' => 'box-001',
            'password' => bcrypt('123456'),
            'name' => 'Box001',
        ]);

        // create a role.
        Role::create([
            'name' => '超级管理员',
            'slug' => 'administrator',
        ]);

        Role::create([
            'name' => '传统箱管理员',
            'slug' => 'box_admin',
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::first());
        Administrator::where('username', 'box-001')->first()->roles()->save(Role::where('slug', 'box_admin')->first());

        //create a permission
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
                'name' => '应用功能设置',
                'slug' => 'configs',
                'http_method' => '',
                'http_path' => "/configs",
            ],
        ];
        $permissions = array_merge($permissions, $this->custom_permissions);
        Permission::insert($permissions);

        // 超管添加所有权限
        Role::first()->permissions()->save(Permission::first());

        // 传统箱管理员添加权限
        Role::where('slug', 'box_admin')->first()->permissions()->save(Permission::where('slug', 'index')->first());
        Role::where('slug', 'box_admin')->first()->permissions()->save(Permission::where('slug', 'auth.login')->first());
        Role::where('slug', 'box_admin')->first()->permissions()->save(Permission::where('slug', 'auth.setting')->first());
        Role::where('slug', 'box_admin')->first()->permissions()->save(Permission::where('slug', 'boxes')->first());
        Role::where('slug', 'box_admin')->first()->permissions()->save(Permission::where('slug', 'box_orders')->first());
        Role::where('slug', 'box_admin')->first()->permissions()->save(Permission::where('slug', 'box_orders.check')->first());


        // add default menus.
        $menus = [
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => '首页',
                'icon' => 'fa-bar-chart',
                'uri' => '/',
                'permission' => 'index',
            ],
            [
                'parent_id' => 0,
                'order' => 999,
                'title' => '系统管理',
                'icon' => 'fa-tasks',
                'uri' => '',
                'permission' => 'auth.management',
            ],
            [
                'parent_id' => 2,
                'order' => 2,
                'title' => '系统信息',
                'icon' => 'fa-dashboard',
                'uri' => 'dashboard',
                'permission' => null,
            ],
            [
                'parent_id' => 2,
                'order' => 3,
                'title' => '管理员',
                'icon' => 'fa-users',
                'uri' => 'auth/users',
                'permission' => null,
            ],
            [
                'parent_id' => 2,
                'order' => 4,
                'title' => '角色',
                'icon' => 'fa-user',
                'uri' => 'auth/roles',
                'permission' => null,
            ],
            [
                'parent_id' => 2,
                'order' => 5,
                'title' => '权限',
                'icon' => 'fa-ban',
                'uri' => 'auth/permissions',
                'permission' => null,
            ],
            [
                'parent_id' => 2,
                'order' => 6,
                'title' => '菜单',
                'icon' => 'fa-bars',
                'uri' => 'auth/menu',
                'permission' => null,
            ],
            [
                'parent_id' => 2,
                'order' => 7,
                'title' => '文件管理',
                'icon' => 'fa-file',
                'uri' => 'media',
                'permission' => null,
            ],
            [
                'parent_id' => 2,
                'order' => 7,
                'title' => '系统日志',
                'icon' => 'fa-database',
                'uri' => 'logs',
                'permission' => null,
            ],
            [
                'parent_id' => 2,
                'order' => 8,
                'title' => '操作日志',
                'icon' => 'fa-history',
                'uri' => 'auth/logs',
                'permission' => null,
            ],
            [
                'parent_id' => 2,
                'order' => 9,
                'title' => 'Horizon',
                'icon' => 'fa-desktop',
                'uri' => 'horizon',
                'permission' => null,
            ],
            [
                'parent_id' => 2,
                'order' => 10,
                'title' => 'Redis',
                'icon' => 'fa-database',
                'uri' => 'redis',
                'permission' => null,
            ],
            [
                'parent_id' => 0,
                'order' => 998,
                'title' => '应用功能设置',
                'icon' => 'fa-gear',
                'uri' => 'configs',
                'permission' => 'configs',
            ],
        ];
        $menus = array_merge($menus, $this->custom_menus);
        Menu::insert($menus);

        // add role to menu.
        Menu::find(2)->roles()->save(Role::first());
        Menu::find(15)->roles()->save(Role::first());/*文章管理*/
    }
}
