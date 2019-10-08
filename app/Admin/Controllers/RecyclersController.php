<?php

namespace App\Admin\Controllers;

use App\Models\Bin;
use App\Models\Recycler;
use App\Http\Requests\Request;
use App\Models\RecyclerWithdraw;
use App\Notifications\Client\AdminCustomNotification;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Foundation\Validation\ValidatesRequests;

class RecyclersController extends AdminController
{
    use ValidatesRequests;

    /**
     * Title for current resource.
     * @var string
     */
    protected $title = '回收员';

    /**
     * Make a grid builder.
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Recycler);
        // $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

        /*筛选*/
        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
                $filter->like('name', '昵称');
                $filter->like('phone', '手机号');
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->between('created_at', '创建时间')->datetime();
                $filter->between('money', '余额');
            });
        });

        $grid->column('id', 'ID')->sortable();
        $grid->column('avatar', '头像')->image('', 40);
        $grid->name('昵称')->display(function ($name) {
            return "<a href='" . route('admin.recyclers.show', $this->id) . "'>$name</a>";
        });
        $grid->column('phone', '手机号');
        $grid->column('money', '余额')->sortable();
        $grid->column('bins', '分配的回收箱')->display(function ($bins) {
            return count($bins);
        });
        $grid->column('orders', '回收订单数')->display(function ($orders) {
            return count($orders);
        });
        $grid->column('manage', '管理')->display(function () {
            $buttons = '';
            $buttons .= '<a class="btn btn-xs btn-primary" style="margin-right:6px" href="' . route('admin.recyclers.send_message.show', ['id' => $this->id]) . '">发送通知</a>';
            $buttons .= '<a class="btn btn-xs btn-primary" style="margin-right:6px" href="' . route('admin.clean_orders.index', ['recycler_id' => $this->id]) . '">回收订单</a>';
            $buttons .= '<a class="btn btn-xs btn-primary" style="margin-right:6px" href="' . route('admin.recyclers.assignment.show', $this->id) . '">分配回收箱</a>';
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
        $show = new Show(Recycler::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('avatar', '头像')->image('', 120);
        $show->field('name', '昵称');
        $show->field('phone', '手机号');
        $show->field('money', '余额');
        $show->field('frozen_money', '冻结金额');
        $show->field('created_at', '创建时间');

        $show->notifications('消息通知', function ($notify) {
            $notify->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

            /*禁用*/
            $notify->disableCreateButton();
            $notify->disableFilter();
            $notify->disableActions();
            $notify->disableBatchActions();

            // grid
            $notify->created_at('时间');
            $notify->column('data', '通知')->display(function ($data) {
                return "$data[title]<br/>$data[info]<br/>";
            });
            $notify->column('read_at', '已读')->bool();
        });

        $show->moneyBills('账单记录', function ($moneyBill) {
            $moneyBill->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

            /*禁用*/
            $moneyBill->disableCreateButton();
            $moneyBill->disableFilter();
            $moneyBill->disableActions();
            $moneyBill->disableBatchActions();

            // grid
            $moneyBill->created_at('时间');
            $moneyBill->type_text('类型');
            $moneyBill->description('描述');
            $moneyBill->operator_number('金额');
        });

        $show->withdraws('提现记录', function ($withdraw) {
            $withdraw->model()->orderBy('created_at', 'desc'); // 设置初始排序条件

            /*禁用*/
            $withdraw->disableCreateButton();
            $withdraw->disableFilter();
            $withdraw->disableActions();
            $withdraw->disableBatchActions();

            // grid
            $withdraw->created_at('时间');
            $withdraw->type_text('到账方式');
            $withdraw->status_text('状态');
            $withdraw->money('金额');
            $withdraw->info('提现预留信息')->display(function ($info) {
                $str = '';
                switch ($this->type) {
                    case RecyclerWithdraw::TYPE_UNION_PAY:
                        $str = "户名:$info[name]<br/>账号:$info[account]<br/>银行:$info[bank]<br/>开户行:$info[bank_name]<br/>";
                        break;
                }
                return $str;
            });
            $withdraw->reason('拒绝原因');
        });

        return $show;
    }

    /**
     * Make a form builder.
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Recycler);

        $form->text('name', '昵称');
        $form->mobile('phone', '手机号');
        $form->image('avatar', '头像')->uniqueName()->move('avatars/' . date('Ym', time()))->rules('required|image');
        $form->decimal('money', '余额')->default(0.00);
        $form->decimal('frozen_money', '冻结金额')->default(0.00);
        // $form->password('password', 'Password');
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        return $form;
    }

    // GET: 回收员指派 页面
    public function assignmentShow(Content $content, $id)
    {
        return $content
            ->header('分配回收箱')
            ->body($this->assignmentForm($id)->edit($id));
    }

    protected function assignmentForm($id)
    {
        $form = new Form(new Recycler);
        $form->setAction(route('admin.recyclers.assignment.store', $id));

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->listbox('bins', '请选择回收箱')->options(Bin::with('site')->get()->pluck('full_name', 'id'));

        return $form;
    }

    // POST: 回收员指派 请求处理
    public function assignmentStore($id)
    {
        return $this->assignmentForm($id)->update($id);
    }

    // GET: 群发站内信 页面
    public function sendMessageShow(Content $content, $id = null)
    {
        return $content
            ->header('发送站内信')
            ->body($this->sendMessageForm($id));
    }

    protected function sendMessageForm($id)
    {
        $form = new Form(new Recycler());
        $form->setAction(route('admin.recyclers.send_message.store'));
        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();
        });

        if ($id == null) {
            $form->listbox('recycler_ids', '选择回收员')->options(Recycler::all()->pluck('name', 'id'));
        } else {
            $form->listbox('recycler_ids', '选择回收员')->options(Recycler::where('id', $id)->get()->pluck('name', 'id'))->default($id);
        }

        $form->text('title', '标题');
        $form->textarea('info', '内容');
        $form->text('link', '链接');

        return $form;
    }

    // POST: 群发站内信 请求处理
    public function sendMessageStore(Request $request, Content $content)
    {
        $data = $this->validate($request, [
            'recycler_ids' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (Recycler::whereIn('id', request()->input($attribute))->count() == 0) {
                        $fail('请选择回收员');
                    }
                },
            ],
            'title' => ['required'],
            'info' => ['required'],
            'link' => ['nullable', 'url'],
        ], [], [
            'recycler_ids' => '回收员',
            'title' => '标题',
            'info' => '内容',
            'link' => '链接',
        ]);

        $recyclers = Recycler::whereIn('id', $data['recycler_ids'])->get();

        $recyclers->each(function ($recycler) use ($data) {
            $recycler->notify(new AdminCustomNotification(array(
                'title' => $data['title'],
                'info' => $data['info'],
                'link' => $data['link'],
            )));
        });

        return $content
            ->row("<center><h3>发送站内信成功</h3></center>")
            ->row("<center><a href='" . route('admin.recyclers.index') . "'>返回回收员列表</a></center>");
    }
}
