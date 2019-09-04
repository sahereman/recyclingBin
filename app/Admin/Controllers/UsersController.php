<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UsersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);
        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        /*筛选*/
        $grid->filter(function ($filter) {
            $filter->disableIdFilter(); // 去掉默认的id过滤器
            $filter->like('name', '用户名');
        });

        $grid->column('id', 'Id')->sortable();
        $grid->avatar('Avatar')->image('', 40);
        // $grid->column('wx_openid', 'Wx openid');
        $grid->column('name', 'Name');
        $grid->column('gender', 'Gender')->sortable();
        $grid->column('phone', 'Phone');
        // $grid->column('avatar', 'Avatar');
        $grid->column('money', 'Money');
        $grid->column('wx_country', 'Wx country');
        $grid->column('wx_province', 'Wx province');
        $grid->column('wx_city', 'Wx city');
        $grid->column('email', 'Email');
        // $grid->column('email_verified_at', 'Email verified at');
        // $grid->column('password', 'Password');
        // $grid->column('created_at', 'Created at');
        // $grid->column('updated_at', 'Updated at');

        // 不在页面显示 `新建` 按钮，因为我们不需要在后台新建用户
        $grid->disableCreateButton();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });;

        $show->field('id', 'Id');
        $show->field('avatar', 'Avatar')->image('', 120);
        $show->field('wx_openid', 'Wx openid');
        $show->field('name', 'Name');
        $show->field('gender', 'Gender');
        $show->field('phone', 'Phone');
        // $show->field('avatar', 'Avatar');
        $show->field('money', 'Money');
        $show->field('wx_country', 'Wx country');
        $show->field('wx_province', 'Wx province');
        $show->field('wx_city', 'Wx city');
        $show->field('email', 'Email');
        $show->field('email_verified_at', 'Email verified at');
        // $show->field('password', 'Password');
        // $show->field('created_at', 'Created at');
        // $show->field('updated_at', 'Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });

        $form->image('avatar', 'Avatar')->uniqueName()->move('avatars/' . date('Ym', time()))->rules('required|image');
        // $form->text('wx_openid', 'Wx openid');
        $form->display('wx_openid', 'Wx openid');
        // $form->text('name', 'Name');
        $form->display('name', 'Name')->rules('required|string');
        // $form->text('gender', 'Gender');
        $form->display('gender', 'Gender');
        // $form->mobile('phone', 'Phone');
        $form->display('phone', 'Phone');
        // $form->image('avatar', 'Avatar');
        // $form->decimal('money', 'Money')->default(0.00);
        $form->display('money', 'Money')->default(0.00);
        // $form->text('wx_country', 'Wx country');
        $form->display('wx_country', 'Wx country');
        // $form->text('wx_province', 'Wx province');
        $form->display('wx_province', 'Wx province');
        // $form->text('wx_city', 'Wx city');
        $form->display('wx_city', 'Wx city');
        // $form->email('email', 'Email');
        $form->display('email', 'Email');
        // $form->datetime('email_verified_at', 'Email verified at'))->default(date('Y-m-d H:i:s');
        // $form->password('password', 'Password');

        return $form;
    }
}