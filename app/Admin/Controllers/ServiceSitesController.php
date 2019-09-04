<?php

namespace App\Admin\Controllers;

use App\Models\ServiceSite;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ServiceSitesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Service Site';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ServiceSite);
        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        $grid->column('id', 'Id')->sortable();
        $grid->column('name', 'Name');
        $grid->column('county', 'County');
        $grid->column('province', 'Province');
        // $grid->column('province_simple', 'Province simple');
        $grid->column('city', 'City');
        // $grid->column('city_simple', 'City simple');
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
        $show = new Show(ServiceSite::findOrFail($id));

        $show->field('id', 'Id');
        $show->field('name', 'Name');
        $show->field('county', 'County');
        $show->field('province', 'Province');
        $show->field('province_simple', 'Province simple');
        $show->field('city', 'City');
        $show->field('city_simple', 'City simple');
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
        $form = new Form(new ServiceSite);

        $form->text('name', 'Name')->rules('required|string');
        $form->text('county', 'County')->rules('required|string');
        $form->text('province', 'Province')->rules('required|string');
        $form->text('province_simple', 'Province simple')->rules('required|string');
        $form->text('city', 'City')->rules('required|string');
        $form->text('city_simple', 'City simple')->rules('required|string');

        return $form;
    }
}
