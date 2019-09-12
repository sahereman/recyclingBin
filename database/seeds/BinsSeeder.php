<?php

use App\Jobs\GenerateBinTypeSnapshot;
use App\Models\Bin;
use App\Models\BinTypeFabric;
use App\Models\BinTypePaper;
use App\Models\ClientPrice;
use App\Models\CleanPrice;
use App\Models\ServiceSite;
use Illuminate\Database\Seeder;
use App\Models\Recycler;
use App\Models\BinRecycler;

class BinsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        //回收员
        $recycler_1 = Recycler::find(1);
        $recycler_2 = Recycler::find(2);

        //客户端价格
        $client_paper_price = ClientPrice::where('slug', 'paper')->first();
        $client_fabric_price = ClientPrice::where('slug', 'fabric')->first();

        //回收端价格
        $clean_paper_price = CleanPrice::where('slug', 'paper')->first();
        $clean_fabric_price = CleanPrice::where('slug', 'fabric')->first();

        // 青岛站
        $site = ServiceSite::where('city', '青岛市')->first();
        $lat = '36.092550';
        $lng = '120.381420';
        for ($i = 1; $i <= 20; $i++)
        {
            $bin = factory(Bin::class)->create([
                'site_id' => $site->id,
                'no' => '053200' . $i,
                'lat' => $lat,
                'lng' => $lng,
            ]);
            $lat = bcadd($lat, '0.001', 6);
            $lng = bcadd($lng, '0.001', 6);

            factory(BinTypePaper::class)->create([
                'bin_id' => $bin->id,
                'client_price_id' => $client_paper_price->id,
                'clean_price_id' => $clean_paper_price->id,
            ]);
            factory(BinTypeFabric::class)->create([
                'bin_id' => $bin->id,
                'client_price_id' => $client_fabric_price->id,
                'clean_price_id' => $clean_fabric_price->id,
            ]);

            GenerateBinTypeSnapshot::dispatch($bin);

            // 回收箱绑定回收员
            if ($i <= 2)
            {
                BinRecycler::create([
                    'bin_id' => $bin->id,
                    'recycler_id' => $recycler_1->id,
                ]);
                BinRecycler::create([
                    'bin_id' => $bin->id,
                    'recycler_id' => $recycler_2->id,
                ]);
            }
        }

        // 济南站
        $site = ServiceSite::where('city', '济南市')->first();
        $lat = '36.660958';
        $lng = '117.016158';
        for ($i = 1; $i <= 5; $i++)
        {
            $bin = factory(Bin::class)->create([
                'site_id' => $site->id,
                'no' => '053100' . $i,
                'lat' => $lat,
                'lng' => $lng,
            ]);
            $lat = bcadd($lat, '0.001', 6);
            $lng = bcadd($lng, '0.001', 6);

            factory(BinTypePaper::class)->create([
                'bin_id' => $bin->id,
                'client_price_id' => $client_paper_price->id,
                'clean_price_id' => $clean_paper_price->id,
            ]);
            factory(BinTypeFabric::class)->create([
                'bin_id' => $bin->id,
                'client_price_id' => $client_fabric_price->id,
                'clean_price_id' => $clean_fabric_price->id,
            ]);

            GenerateBinTypeSnapshot::dispatch($bin);

            // 回收箱绑定回收员
            if ($i <= 2)
            {
                BinRecycler::create([
                    'bin_id' => $bin->id,
                    'recycler_id' => $recycler_1->id,
                ]);
                BinRecycler::create([
                    'bin_id' => $bin->id,
                    'recycler_id' => $recycler_2->id,
                ]);
            }
        }
    }
}
