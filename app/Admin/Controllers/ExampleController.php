<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ExampleController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Example controller';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ExampleModel);
        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        // 禁用创建按钮
        $grid->disableCreateButton();
        // 禁用分页条
        $grid->disablePagination();
        // 禁用查询过滤器
        $grid->disableFilter();
        // 禁用导出数据按钮
        $grid->disableExport();
        // 禁用行选择 checkbox
        $grid->disableRowSelector();
        // 禁用行操作列
        $grid->disableActions();
        // 禁用行选择器
        $grid->disableColumnSelector();
        // 设置分页选择器选项
        $grid->perPages([10, 20, 30, 40, 50]);
        // 设置每页显示行数
        // 默认为每页20条
        $grid->paginate(15);

        /*禁用*/
        // $grid->disableCreation(); // Deprecated
        $grid->disableCreateButton();

        // 禁用筛选
        // $grid->disableFilter();

        /*自定义筛选框*/
        $grid->filter(function ($filter) {
            $filter->disableIdFilter(); // 去掉默认的id过滤器
            $filter->like('name', 'Name');
        });

        // 关闭全部操作
        /*$grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            // 去掉编辑
            $actions->disableEdit();
            // 去掉查看
            $actions->disableView();
            // 当前行的数据数组
            $actions->row;
            // 获取当前行主键值
            $actions->getKey();
            // 添加操作
            $actions->append(new CheckRow($actions->getKey()));
        });*/
        $grid->disableActions();

        // 去掉批量操作
        /*$grid->batchActions(function ($batch) {
            $batch->disableDelete();
        });*/
        $grid->disableBatchActions();

        $grid->column('id', __('ID'))->sortable();
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(ExampleModel::findOrFail($id));

        $show->panel()->tools(function ($tools) {
            $tools->disableList();
            $tools->disableEdit();
            $tools->disableDelete();
            $tools->prepend('<div class="btn-group pull-right" style="margin-right: 5px">'
                . '<a href="' . route('admin.products.sku_editor_show', ['product' => $id]) . '" class="btn btn-sm btn-success">'
                . '<i class="fa fa-archive"></i>&nbsp;SKU 编辑器'
                . '</a>'
                . '</div>&nbsp;'
                . '<div class="btn-group pull-right" style="margin-right: 5px">'
                . '<a href="' . route('admin.product_faqs.index', ['product_id' => $id]) . '" class="btn btn-sm btn-success">'
                . '<i class="fa fa-list"></i>&nbsp;FAQ - 列表'
                . '</a>'
                . '</div>&nbsp;');
            $tools->append('<div class="btn-group pull-right" style="margin-right: 5px">'
                . '<a href="' . route('admin.products.sku_editor_show', ['product' => $id]) . '" class="btn btn-sm btn-success">'
                . '<i class="fa fa-archive"></i>&nbsp;SKU 编辑器'
                . '</a>'
                . '</div>&nbsp;'
                . '<div class="btn-group pull-right" style="margin-right: 5px">'
                . '<a href="' . route('admin.product_faqs.index', ['product_id' => $id]) . '" class="btn btn-sm btn-success">'
                . '<i class="fa fa-list"></i>&nbsp;FAQ - 列表'
                . '</a>'
                . '</div>&nbsp;');
        });

        $show->field('id', __('ID'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        /* one to one */
        $show->user('User', function ($user) {
            /*禁用*/
            $user->panel()->tools(function ($tools) {
                $tools->disableList();
                $tools->disableEdit();
                $tools->disableDelete();
            });

            $user->name('Name');
            $user->gender('Gender');
            $user->phone('Phone');
        });

        /* one to many */
        $show->children('Children', function ($child) {
            /*禁用*/
            // $child->disableCreation(); // Deprecated
            $child->disableCreateButton();

            // 禁用筛选
            $child->disableFilter();

            // 禁用导出数据按钮
            $child->disableExport();

            // 关闭全部操作
            /*$child->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                // 去掉查看
                $actions->disableView();
            });*/
            $child->disableActions();

            // 去掉批量操作
            /*$child->batchActions(function ($batch) {
                $batch->disableDelete();
            });*/
            $child->disableBatchActions();

            // $child->id('ID');
            // $child->name('Name');
            // ...
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ExampleModel);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            $tools->disableEdit();
            $tools->disableDelete();
            $tools->prepend('<div class="btn-group pull-right" style="margin-right: 5px">'
                . '<a href="' . route('admin.products.sku_editor_show', ['product' => $id]) . '" class="btn btn-sm btn-success">'
                . '<i class="fa fa-archive"></i>&nbsp;SKU 编辑器'
                . '</a>'
                . '</div>&nbsp;'
                . '<div class="btn-group pull-right" style="margin-right: 5px">'
                . '<a href="' . route('admin.product_faqs.index', ['product_id' => $id]) . '" class="btn btn-sm btn-success">'
                . '<i class="fa fa-list"></i>&nbsp;FAQ - 列表'
                . '</a>'
                . '</div>&nbsp;');
            $tools->append('<div class="btn-group pull-right" style="margin-right: 5px">'
                . '<a href="' . route('admin.products.sku_editor_show', ['product' => $id]) . '" class="btn btn-sm btn-success">'
                . '<i class="fa fa-archive"></i>&nbsp;SKU 编辑器'
                . '</a>'
                . '</div>&nbsp;'
                . '<div class="btn-group pull-right" style="margin-right: 5px">'
                . '<a href="' . route('admin.product_faqs.index', ['product_id' => $id]) . '" class="btn btn-sm btn-success">'
                . '<i class="fa fa-list"></i>&nbsp;FAQ - 列表'
                . '</a>'
                . '</div>&nbsp;');
        });

        $form->display('id', __('ID'));
        $form->display('created_at', __('Created At'));
        $form->display('updated_at', __('Updated At'));

        /* one to many */
        $form->hasMany('children', 'Children', function ($child) {
            // $child->display('id', 'ID')->setWidth(2);
            // $child->display('name', 'Name')->setWidth(2);
            // ...
        })->disableCreate()->disableDelete()->readOnly();

        return $form;
    }
}
