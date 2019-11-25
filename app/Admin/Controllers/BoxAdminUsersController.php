<?php

namespace App\Admin\Controllers;

use App\Models\Administrator;
use App\Models\Box;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class BoxAdminUsersController extends AdminController
{
    /**
     * Title for current resource.
     * @var string
     */
    protected $title = '传统箱管理员';

    /**
     * Make a grid builder.
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Administrator);

        $role = Role::where('slug','box_admin')->first();
        $admin_user_ids = $role->administrators->pluck('id')->all();
        $grid->model()->whereIn('id',$admin_user_ids)->orderBy('created_at', 'desc'); // 设置初始排序条件

        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
                $filter->like('name', '姓名/昵称');
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->like('username', '用户名');
                $filter->between('created_at', '创建时间')->datetime();
            });
        });

        $grid->disableExport();

        $grid->column('id', 'ID')->sortable();
        $grid->avatar('头像')->image('', 40);
        $grid->username('用户名');
        $grid->name('姓名/昵称');
        $grid->column('boxes', '分配的传统箱')->display(function ($boxes) {
            return count($boxes);
        });
        $grid->column('created_at', '创建时间')->sortable();
        $grid->column('manage', '管理')->display(function () {
            $buttons = '';
            $buttons .= '<a class="btn btn-xs btn-primary" style="margin-right:6px" href="' . route('admin.box_admin_users.assignment.show', $this->id) . '">分配传统箱</a>';
            return $buttons;
        });

        return $grid;
    }

    /**
     * Make a show builder.
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Administrator::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('username', '用户名');
        $show->field('name', '姓名/昵称');
        $show->field('avatar', '头像')->image();
        $show->field('created_at', '创建时间');

        return $show;
    }

    /**
     * Make a form builder.
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Administrator);

        $form->text('username', '用户名');
        $form->text('name', '姓名/昵称');

        $form->password('password', '密码')->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });
        $form->image('avatar', '头像');


        $form->ignore(['password_confirmation']);

        $form->saving(function (Form $form) {
            if ($form->model()->avatar == null)
            {
                $form->model()->avatar = url('/vendor/laravel-admin/AdminLTE/dist/img/user2-160x160.jpg');
            }

            if ($form->password && $form->model()->password != $form->password)
            {
                $form->password = bcrypt($form->password);
            }
        });

        return $form;
    }

    // GET: 分配传统箱 页面
    public function assignmentShow(Content $content, $id)
    {
        return $content
            ->header('分配传统箱')
            ->body($this->assignmentForm($id)->edit($id));
    }

    protected function assignmentForm($id)
    {
        $form = new Form(new Administrator());
        $form->setAction(route('admin.box_admin_users.assignment.store', $id));

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->listbox('boxes', '请选择传统箱')->options(Box::with('site')->get()->pluck('full_name', 'id'));

        return $form;
    }

    // POST: 分配传统箱 请求处理
    public function assignmentStore($id)
    {
        return $this->assignmentForm($id)->update($id);
    }
}
