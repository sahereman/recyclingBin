<?php

use App\Models\ClientPrice;
use Illuminate\Database\Seeder;

class ClientPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        ClientPrice::create([
            'slug' => 'paper',
            'price' => '0.50',
            'unit' => '公斤',
        ]);

        ClientPrice::create([
            'slug' => 'fabric',
            'price' => '0.10',
            'unit' => '公斤',
        ]);
    }
}
