<?php

namespace App\Admin\Controllers;

use App\Models\TopicCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TopicCategoriesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '话题分类';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TopicCategory);
        $grid->model()->orderBy('sort', 'desc'); // 设置初始排序条件
        $grid->disableExport();

        /*自定义筛选框*/
        $grid->filter(function ($filter) {
            $filter->disableIdFilter(); // 去掉默认的id过滤器
            $filter->like('name', '话题分类名称');
        });

        $grid->column('id', 'Id')->sortable();
        $grid->column('name', '话题分类名称')->sortable();
        $grid->column('sort', '排序值')->sortable();
        $grid->column('topics', '话题数目')->display(function ($topics) {
            return count($topics);
        });
        // $grid->column('created_at', '创建时间');
        // $grid->column('updated_at', '更新时间');

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
        $show = new Show(TopicCategory::findOrFail($id));

        // $show->field('id', 'Id');
        $show->field('name', '话题分类名称');
        $show->field('sort', '排序值');
        $show->field('created_at', '创建时间');
        $show->field('updated_at', '更新时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new TopicCategory);

        $form->text('name', '话题分类名称')->rules('required|string');
        $form->number('sort', '排序值')->default(9)->rules('required|integer|min:0')->help('默认倒序排列：数值越大越靠前');

        return $form;
    }
}
