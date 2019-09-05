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

        $grid->column('id', 'Id')->sortable();
        $grid->column('name', 'Name')->sortable();
        $grid->column('sort', 'Sort')->sortable();
        // $grid->column('created_at', 'Created at');
        // $grid->column('updated_at', 'Updated at');

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
        $show->field('name', 'Name');
        $show->field('sort', 'Sort');
        $show->field('created_at', 'Created at');
        $show->field('updated_at', 'Updated at');

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

        $form->text('name', 'Name')->rules('required|string');
        $form->number('sort', 'Sort')->default(9)->rules('required|integer|min:0')->help('默认倒序排列：数值越大越靠前');

        return $form;
    }
}
