<?php

use App\Models\CleanPrice;
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
        CleanPrice::create([
            'slug' => 'paper',
            'price' => '0.70'
        ]);

        CleanPrice::create([
            'slug' => 'fabric',
            'price' => '0.40'
        ]);
    }
}
