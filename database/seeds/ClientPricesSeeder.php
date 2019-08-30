<?php

use Illuminate\Database\Seeder;
use App\Models\ClientPrice;

class ClientPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ClientPrice::create([
            'slug' => 'paper',
            'price' => '0.50'
        ]);

        ClientPrice::create([
            'slug' => 'fabric',
            'price' => '0.10'
        ]);

        ClientPrice::create([
            'slug' => 'harmful',
            'price' => '0.00'
        ]);
    }
}
