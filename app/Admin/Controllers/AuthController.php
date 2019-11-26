<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseAuthController
{

    /**
     * 传统箱管理员跳转 重定向至 有权限的路由中
     * 解决问题 : laravels环境中,无权限会抛出500服务器错误,因box_admin默认进入路由是 /admin
     * Pjax 中的 exit 在 56 行  throw new SwooleExitException($res);
     */
    public function redirectTo()
    {
        $prefix = config('admin.route.prefix');
        $admin_user = Auth::guard('admin')->user();

        if ($admin_user->isRole('box_admin'))
        {
            redirect()->setIntendedUrl($prefix . '/boxes');
        }
    }

}
