<?php

namespace App\Admin\Controllers;

use App\Models\Banner;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BannersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Banner';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Banner);

        $grid->disableExport();
        $grid->model()->orderBy('sort', 'desc'); // 设置初始排序条件
        $grid->id("ID")->hide();
        $grid->image_url('Banner')->image('', 120);
        $grid->column('slug', 'Slug')->sortable();
        // $grid->column('image', 'Image');
        // $grid->column('link', 'Link');
        $grid->column('sort', 'Sort')->sortable();

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
        $show = new Show(Banner::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('slug', 'Slug');
        // $show->field('image', 'Image');
        $show->image_url('Banner')->image('', 300);
        $show->field('link', 'Link');
        $show->field('sort', 'Sort');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Banner);

        // $form->text('slug', 'Slug');
        $form->select('slug', '标示位')->options(Banner::$SlugMap)->rules('required');
        $form->image('image', 'Image')->rules('required|image');
        $form->url('link', 'Link')->default('');
        // $form->number('sort','Sort')->default(10);
        $form->number('sort', '排序值')->default(9)->rules('required|integer|min:0')->help('默认倒序排列：数值越大越靠前');

        return $form;
    }
}
