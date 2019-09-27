<?php

use Illuminate\Database\Seeder;
use App\Models\ClientOrder;
use App\Models\ClientOrderItem;
use App\Admin\Models\Bin;
use App\Models\UserMoneyBill;
use App\Models\User;
use App\Jobs\GenerateClientOrderSnapshot;
use App\Notifications\Client\ClientOrderCompletedNotification;

class ClientOrdersSeeder extends Seeder
{
    public function run()
    {
        $user = User::find(1);
        $bins = Bin::all();

        for ($i = 1; $i <= 20; $i++) {
            $bin = $bins->random();
            $order = factory(ClientOrder::class)->create([
                'bin_id' => $bin->id,
                'user_id' => $user->id,
            ]);

            factory(ClientOrderItem::class, random_int(1, 2))->create([
                'order_id' => $order->id,
            ]);

            GenerateClientOrderSnapshot::dispatch($order, $bin);
            UserMoneyBill::change($user, UserMoneyBill::TYPE_CLIENT_ORDER, $order->total, $order);
            $order->user->notify(new ClientOrderCompletedNotification($order));
        }
    }
}
