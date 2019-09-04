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
    protected $title = 'Topic';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Topic);
        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        $grid->column('id', 'Id')->sortable();
        $grid->column('thumb', 'Thumb')->image('', 40);
        // $grid->column('category_id', 'Category id');
        $grid->column('category_name', 'Category');
        $grid->column('title', 'Title');
        // $grid->column('thumb', 'Thumb')->image('', 40);
        // $grid->column('image', 'Image');
        // $grid->column('content', 'Content');
        $grid->column('is_index', 'Is Index')->switch([
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
        $show->field('category_name', 'Category');
        $show->field('title', 'Title');
        $show->field('thumb', 'Thumb')->image('', 60);
        $show->field('image', 'Image')->image('', 120);
        $show->field('content', 'Content');
        // $show->field('content_simple', 'Content');
        $show->field('is_index', 'Is index')->as(function ($is_index) {
            return $is_index ? 'Yes' : 'No';
        });
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
        Form::extend('editor', \Encore\WangEditor\Editor::class);
        $form = new Form(new Topic);

        // $form->number('category_id', 'Category id');
        $categories = TopicCategory::all()->pluck('name', 'id')->toArray();
        $form->select('category_id', 'Category')->options($categories);
        $form->text('title', 'Title')->rules('required|string');
        $form->image('thumb', 'Thumb')->move('topic/thumbs/' . date('Ym', time()))->rules('required|image');
        $form->image('image', 'Image')->move('topic/images/' . date('Ym', time()))->rules('required|image');
        // $form->textarea('content', 'Content');
        $form->editor('content', 'Content')->rules('required|string');
        $form->switch('is_index', 'Is index')->states([
            'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
        ])->default(0);

        return $form;
    }
}
