<?php

use Illuminate\Database\Seeder;
use App\Models\ClientOrder;
use App\Models\ClientOrderItem;
use App\Admin\Models\Bin;

class ClientOrdersSeeder extends Seeder
{
    public function run()
    {
        $user = \App\Models\User::find(1);
        $bins = \App\Models\Bin::all();

        for ($i = 1; $i <= 20; $i++)
        {
            $order = factory(ClientOrder::class)->create([
                'user_id' => $user->id,
            ]);

            factory(ClientOrderItem::class, random_int(1, 2))->create([
                'order_id' => $order,
            ]);

            \App\Jobs\GenerateClientOrderSnapshot::dispatch($order,$bins->random());
        }
    }
}
