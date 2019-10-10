<?php

namespace App\Admin\Controllers;

use App\Models\Topic;
use App\Models\TopicCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TopicsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '话题';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Topic);
        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        /*自定义筛选框*/
        $grid->filter(function ($filter) {
            $filter->disableIdFilter(); // 去掉默认的id过滤器
            $filter->like('title', '话题标题');
            $filter->like('content', '话题内容');
            $filter->where(function ($query) {
                switch ($this->input) {
                    case 'yes':
                        $query->where('is_index', true);
                        break;
                    case 'no':
                        $query->where('is_index', false);
                        break;
                    default:
                        break;
                }
            }, '正在运行')->radio([
                'all' => '不选择',
                'yes' => '是',
                'no' => '否',
            ]);
        });

        $grid->column('id', 'Id')->sortable();
        $grid->column('thumb', '缩略图')->image('', 40);
        // $grid->column('category_id', 'Category id');
        $grid->column('category_name', '话题分类');
        $grid->column('title', '话题标题');
        $grid->column('view_count', '浏览量');
        // $grid->column('thumb', '缩略图')->image('', 40);
        // $grid->column('image', '图片');
        // $grid->column('content', '话题内容');
        $grid->column('is_index', '是否首页显示')->switch([
            'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
        ]);
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
        $show = new Show(Topic::findOrFail($id));

        // $show->field('id', 'Id');
        // $show->field('category_id', 'Category id');
        $show->field('category_name', '话题分类');
        $show->field('title', '话题标题');
        $show->field('view_count', '浏览量');
        $show->field('thumb', '缩略图')->image('', 60);
        $show->field('image', '图片')->image('', 120);
        $show->content('话题内容');
        $show->field('content', '话题内容');
        // $show->field('content_simple', '话题内容');
        $show->field('is_index', '是否首页显示')->as(function ($is_index) {
            return $is_index ? 'Yes' : 'No';
        });
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
        $form = new Form(new Topic);

        // $form->number('category_id', 'Category id');
        $categories = TopicCategory::all()->pluck('name', 'id')->toArray();
        $form->select('category_id', '话题分类')->options($categories);
        $form->text('title', '话题标题')->rules('required|string');
        $form->number('view_count', '浏览量')->rules('required|integer');
        // $form->image('thumb', '缩略图')->move('topic/thumbs/' . date('Ym', time()))->rules('required|image');
        $form->image('image', '图片')->move('topic/images/' . date('Ym', time()))->rules('required|image');
        // $form->textarea('content', '话题内容');
        $form->editor('content', '话题内容')->rules('required|string');
        $form->switch('is_index', '是否首页显示')->states([
            'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
        ])->default(0);

        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            //
        });

        $form->saved(function (Form $form) {
            // $image = $form->model()->image_url;
            $image = $form->model()->getAttribute('image');
            $form->model()->update([
                'thumb' => $image,
            ]);
        });

        return $form;
    }
}
