<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\UserWithdraw;
use App\Models\UserMoneyBill;
use App\Notifications\Client\UserWithdrawAgreeNotification;
use App\Notifications\Client\UserWithdrawDenyNotification;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // 通过 factory 方法生成 x 个数据并保存到数据库中
        factory(User::class, 5)->create();

        // 单独处理第一个用户的数据
        $user = User::find(1);
        $user->phone = '18600982820';
        $user->save();

        // 提现数据
        for ($i = 1; $i <= 10; $i++)
        {
            $withdraw = factory(UserWithdraw::class)->create([
                'user_id' => $user,
            ]);

            switch ($withdraw->type)
            {
                case UserWithdraw::TYPE_UNION_PAY :
                    $withdraw->update([
                        'info' => [
                            'name' => '张三',
                            'bank' => '中国农业银行',
                            'account' => '62223078323174632',
                            'bank_name' => 'XXX支行',
                        ]
                    ]);
                    break;
            }

            switch ($withdraw->status)
            {
                case UserWithdraw::STATUS_WAIT :
                    $user->update([
                        'frozen_money' => bcadd($user->frozen_money, $withdraw->money, 2)
                    ]);
                    break;
                case UserWithdraw::STATUS_AGREE :
                    UserMoneyBill::change($user, UserMoneyBill::TYPE_USER_WITHDRAW, $withdraw->money, $withdraw);
                    $withdraw->user->notify(new UserWithdrawAgreeNotification($withdraw));
                    break;
                case UserWithdraw::STATUS_DENY :
                    $withdraw->update([
                        'reason' => '银行预留信息错误'
                    ]);
                    $withdraw->user->notify(new UserWithdrawDenyNotification($withdraw));
                    break;
            }

        }


        //单独处理第二个用户的数据
        // $user = \App\Models\User::find(2);
        // $user->phone = '17863972036';
        // $user->save();
    }
}
