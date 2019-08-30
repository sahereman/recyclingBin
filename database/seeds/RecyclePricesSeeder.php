<?php

use Illuminate\Database\Seeder;
use App\Models\RecyclePrice;

class RecyclePricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RecyclePrice::create([
            'slug' => 'paper',
            'price' => '0.70'
        ]);

        RecyclePrice::create([
            'slug' => 'fabric',
            'price' => '0.40'
        ]);

        RecyclePrice::create([
            'slug' => 'harmful',
            'price' => '0.00'
        ]);
    }
}
