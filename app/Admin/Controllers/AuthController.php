<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseAuthController
{
    /**
     * Get the post login redirect path.
     * @return string
     */
    protected function redirectPath()
    {
        if (method_exists($this, 'redirectTo'))
        {
            return $this->redirectTo();
        }

        $admin_user_role = Auth::guard('admin')->user()->roles->first();

        if ($admin_user_role->slug == 'box_admin')
        {
            return property_exists($this, 'redirectTo') ? $this->redirectTo : config('admin.route.prefix') . '/boxes';
        } else
        {
            return property_exists($this, 'redirectTo') ? $this->redirectTo : config('admin.route.prefix');
        }

    }

}
