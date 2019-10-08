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
    protected $title = '回收站点';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ServiceSite);
        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        /*自定义筛选框*/
        $grid->filter(function ($filter) {
            $filter->disableIdFilter(); // 去掉默认的id过滤器
            $filter->like('name', '回收站点名称');
        });

        $grid->column('id', 'Id')->sortable();
        $grid->column('name', '回收站点名称');
        $grid->column('county', '国家');
        $grid->column('province', '省份');
        // $grid->column('province_simple', '省份简称');
        $grid->column('city', '城市');
        // $grid->column('city_simple', '城市简称');
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
        $show = new Show(ServiceSite::findOrFail($id));

        $show->field('id', 'Id');
        $show->field('name', '回收站点名称');
        $show->field('county', '国家');
        $show->field('province', '省份');
        $show->field('province_simple', '省份简称');
        $show->field('city', '城市');
        $show->field('city_simple', '城市简称');
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
        $form = new Form(new ServiceSite);

        $form->text('name', '回收站点名称')->rules('required|string');
        $form->text('county', '国家')->rules('required|string');
        $form->text('province', '省份')->rules('required|string');
        $form->text('province_simple', '省份简称')->rules('required|string');
        $form->text('city', '城市')->rules('required|string');
        $form->text('city_simple', '城市简称')->rules('required|string');

        return $form;
    }
}
