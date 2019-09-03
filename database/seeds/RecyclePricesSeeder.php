<?php

use App\Models\RecyclePrice;
use Illuminate\Database\Seeder;

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
    }
}
