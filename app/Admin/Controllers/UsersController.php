<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Ajax\Ajax_Button;
use App\Models\User;
use App\Models\UserWithdraw;
use App\Notifications\Client\AdminCustomNotification;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;

class UsersController extends AdminController
{
    use ValidatesRequests;

    /**
     * Title for current resource.
     * @var string
     */
    protected $title = '用户';

    /**
     * Make a grid builder.
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);
        //        $grid->model()->orderBy('created_at', 'desc'); // 设置初始排序条件
        $grid->disableCreateButton();

        /*筛选*/
        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
                $filter->like('name', '昵称');
                $filter->like('phone', '手机号');
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->between('created_at', '创建时间')->datetime();
                $filter->between('money', '奖励金');
                $filter->where(function ($query) {
                    switch ($this->input)
                    {
                        case 'yes':
                            $query->where('real_authenticated_at', '!=', null);
                            break;
                        case 'no':
                            $query->where('real_authenticated_at', null);
                            break;
                    }
                }, '已实名')->radio([
                    'all' => '不选择',
                    'yes' => '是',
                    'no' => '否',
                ]);
                $filter->where(function ($query) {
                    switch ($this->input)
                    {
                        case 'yes':
                            $query->where('disabled_at', '!=', null);
                            break;
                        case 'no':
                            $query->where('disabled_at', null);
                            break;
                    }
                }, '黑名单')->radio([
                    'all' => '不选择',
                    'yes' => '是',
                    'no' => '否',
                ]);
            });
        });

        // grid
        $grid->column('id', 'ID')->sortable();
        $grid->avatar('头像')->image('', 40);
        $grid->name('昵称')->display(function ($name) {
            return "<a href='" . route('admin.users.show', $this->id) . "'>$name</a>";
        });
        $grid->column('gender', '性别')->sortable();
        $grid->column('phone', '手机号');
        $grid->column('money', '奖励金')->sortable();
        $grid->column('total_client_order_count', '累计投递订单次数')->sortable();
        $grid->column('created_at', '创建时间')->sortable();

        $grid->column('manage', '管理')->display(function () {
            $buttons = '';
            $buttons .= '<a class="btn btn-xs btn-primary" style="margin-right:6px" href="' . route('admin.users.send_message.show', ['id' => $this->id]) . '">发送通知</a>';
            $buttons .= '<a class="btn btn-xs btn-primary" style="margin-right:6px" href="' . route('admin.client_orders.index', ['user_id' => $this->id]) . '">投递订单</a>';

            if ($this->disabled_at == null)
            {
                $buttons .= new Ajax_Button(route('admin.users.disable', $this->id), [], '加入黑名单');
            } else
            {
                $buttons .= new Ajax_Button(route('admin.users.enable', $this->id), [], '移除黑名单');
            }

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
        $show = new Show(User::findOrFail($id));

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });;

        $show->field('id', 'ID');
        $show->field('avatar', '头像')->image('', 120);
        $show->field('name', '昵称');
        $show->field('gender', '性别');
        $show->field('phone', '手机号');
        $show->field('money', '奖励金');
        $show->field('frozen_money', '冻结金额');
        $show->field('total_client_order_money', '累计投递订单金额');
        $show->field('total_client_order_count', '累计投递订单次数');
        $show->field('wx_openid', '微信 OpenId');
        $show->field('wx_country', '微信 Country');
        $show->field('wx_province', '微信 Province');
        $show->field('wx_city', '微信 City');
        $show->field('real_id', '身份证号');
        $show->field('real_name', '真实姓名');
        $show->field('real_authenticated_at', '实名认证时间');
        //        $show->field('email', 'Email');
        //        $show->field('email_verified_at', 'Email verified at');
        // $show->field('password', 'Password');
        // $show->field('created_at', 'Created at');
        // $show->field('updated_at', 'Updated at');

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
                switch ($this->type)
                {
                    case UserWithdraw::TYPE_UNION_PAY:
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
        $form = new Form(new User);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        $form->display('id', 'ID');
        $form->image('avatar', '头像')->uniqueName()->move('avatars/' . date('Ym', time()))->rules('required|image');
        $form->text('name', '昵称')->rules('required|string');
        $form->radio('gender', '性别')->options(['男' => '男', '女' => '女'])->default('男')->rules('required|in:男,女');
        $form->mobile('phone', '手机号');
        $form->display('money', '奖励金');
        $form->display('frozen_money', '冻结金额');
        $form->display('total_client_order_money', '累计投递订单金额');
        $form->display('total_client_order_count', '累计投递订单次数');
        $form->display('wx_openid', '微信 openid');
        $form->display('wx_country', '微信 country');
        $form->display('wx_province', '微信 province');
        $form->display('wx_city', '微信 city');
        $form->display('real_id', '身份证号');
        $form->display('real_name', '真实姓名');
        $form->display('real_authenticated_at', '实名认证时间');
        /*$form->switch('is_authenticated', '已实名')->states([
            'on' => ['value' => 1, 'text' => 'YES', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => 'NO', 'color' => 'default'],
        ]);*/
        // $form->email('email', 'Email');
        // $form->display('email', 'Email');
        // $form->datetime('email_verified_at', 'Email verified at'))->default(date('Y-m-d H:i:s');
        // $form->password('password', 'Password');

        return $form;
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
        $form = new Form(new User());
        $form->setAction(route('admin.users.send_message.store'));
        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();
        });

        if ($id == null)
        {
            $form->listbox('user_ids', '选择用户')->options(User::all()->pluck('name', 'id'))->required();
        } else
        {
            $form->listbox('user_ids', '选择用户')->options(User::where('id', $id)->get()->pluck('name', 'id'))->default($id)->required();
        }

        $form->text('title', '标题')->required();
        $form->textarea('info', '内容')->required();
        $form->text('link', '链接');

        return $form;
    }

    // POST: 群发站内信 请求处理
    public function sendMessageStore(Request $request, Content $content)
    {
        $data = $this->validate($request, [
            'user_ids' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (User::whereIn('id', request()->input($attribute))->count() == 0)
                    {
                        $fail('请选择用户');
                    }
                },
            ],
            'title' => ['required'],
            'info' => ['required'],
            'link' => ['nullable'],
        ], [], [
            'user_ids' => '用户',
            'title' => '标题',
            'info' => '内容',
            'link' => '链接',
        ]);

        $users = User::whereIn('id', $data['user_ids'])->get();

        $users->each(function ($user) use ($data) {
            $user->notify(new AdminCustomNotification(array(
                'title' => $data['title'],
                'info' => $data['info'],
                'link' => $data['link'],
            )));
        });

        return $content
            ->row("<center><h3>发送站内信成功</h3></center>")
            ->row("<center><a href='" . route('admin.users.index') . "'>返回用户列表</a></center>");
    }

    public function disable(Request $request, User $user)
    {
        $user->update([
            'disabled_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => '加入黑名单成功'
        ]);
    }

    public function enable(Request $request, User $user)
    {
        $user->update([
            'disabled_at' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => '移除黑名单成功'
        ]);
    }
}
