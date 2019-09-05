<?php

namespace App\Admin\Controllers;

use App\Models\Bin;
use App\Models\ServiceSite;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BinsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '回收垃圾桶';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Bin);
        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        $grid->column('id', 'Id')->sortable();
        // $grid->column('site_id', 'Site id');
        $grid->site()->name('Site');
        $grid->column('is_run', 'Is Run')->switch([
            'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
        ]);
        $grid->column('name', 'Name');
        $grid->column('no', 'No.');
        $grid->column('lat', 'Latitude');
        $grid->column('lng', 'Longitude');
        $grid->column('address', 'Address');
        // $grid->column('types_snapshot', 'Types snapshot');
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
        $show = new Show(Bin::findOrFail($id));

        $show->field('id', 'Id');
        // $show->field('site_id', 'Site id');
        $show->field('site_name', 'Site');
        $show->field('is_run', 'Is Run')->as(function ($is_run) {
            return $is_run ? 'Yes' : 'No';
        });
        $show->field('name', 'Name');
        $show->field('no', 'No.');
        $show->field('lat', 'Latitude');
        $show->field('lng', 'Longitude');
        $show->field('address', 'Address');
        // $show->field('types_snapshot', 'Types snapshot')->json();
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
        $form = new Form(new Bin);

        // $form->number('site_id', 'Site id');
        $sites = ServiceSite::all()->pluck('name', 'id')->toArray();
        $form->select('site_id', 'Site')->options($sites);
        // $form->switch('is_run', 'Is Run')->default(1);
        $form->switch('is_run', 'Is Run')->states([
            'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
        ])->default(1);
        $form->text('name', 'Name')->rules('required|string');
        $form->text('no', 'No.')->rules('required|string');
        $form->text('lat', 'Latitude')->rules('required|string');
        $form->text('lng', 'Longitude')->rules('required|string');
        $form->text('address', 'Address')->rules('required|string');
        // $form->text('types_snapshot', 'Types snapshot');

        return $form;
    }
}
