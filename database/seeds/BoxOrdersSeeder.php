<?php

use Illuminate\Database\Seeder;
use App\Models\BoxOrder;
use App\Models\Box;
use App\Models\UserMoneyBill;
use App\Models\User;
use App\Notifications\Client\ClientOrderCompletedNotification;

class BoxOrdersSeeder extends Seeder
{
    public function run()
    {
        $user = User::find(1);
        $boxes = Box::all();

        for ($i = 1; $i <= 20; $i++) {
            $box = $boxes->random();
            $order = factory(BoxOrder::class)->create([
                'box_id' => $box->id,
                'user_id' => $user->id,
            ]);

            UserMoneyBill::change($user, UserMoneyBill::TYPE_BOX_ORDER, $order->total, $order);
//            $order->user->notify(new ClientOrderCompletedNotification($order));
        }
    }
}
