<?php

use Illuminate\Database\Seeder;
use App\Models\Bin;
use App\Models\ServiceSite;
use App\Models\BinTypePaper;
use App\Models\BinTypeFabric;
use App\Models\ClientPrice;
use App\Models\RecyclePrice;

class BinsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        //客户端价格
        $client_paper_price = ClientPrice::where('slug', 'paper')->first();
        $client_fabric_price = ClientPrice::where('slug', 'fabric')->first();
        $client_harmful_price = ClientPrice::where('slug', 'harmful')->first();

        //回收端价格
        $recyc_paper_price = RecyclePrice::where('slug', 'paper')->first();
        $recyc_fabric_price = RecyclePrice::where('slug', 'fabric')->first();
        $recyc_harmful_price = RecyclePrice::where('slug', 'harmful')->first();


        // 青岛站
        $qd_site = ServiceSite::where('city', '青岛市')->first();
        $qd_lat = '36.092550';
        $qd_lng = '120.381420';
        for ($i = 1; $i <= 20; $i++)
        {
            $bin = factory(Bin::class)->create([
                'site_id' => $qd_site->id,
                'no' => '053200' . $i,
                'lat' => $qd_lat,
                'lng' => $qd_lng,
            ]);
            $qd_lat = bcadd($qd_lat, '0.001', 6);
            $qd_lng = bcadd($qd_lng, '0.001', 6);

            factory(BinTypePaper::class)->create([
                'bin_id' => $bin->id,
                'client_price_id' => $client_paper_price->id,
                'recycle_price_id' => $recyc_paper_price->id,
            ]);
            factory(BinTypeFabric::class)->create([
                'bin_id' => $bin->id,
                'client_price_id' => $client_fabric_price->id,
                'recycle_price_id' => $recyc_fabric_price->id,
            ]);

        }


        // 济南站
        $jn_site = ServiceSite::where('city', '济南市')->first();
        $jn_lat = '36.660958';
        $jn_lng = '117.016158';
        for ($i = 1; $i <= 5; $i++)
        {
            factory(Bin::class)->create([
                'site_id' => $jn_site->id,
                'no' => '053100' . $i,
                'lat' => $jn_lat,
                'lng' => $jn_lng,
            ]);
            $jn_lat = bcadd($jn_lat, '0.001', 6);
            $jn_lng = bcadd($jn_lng, '0.001', 6);

            factory(BinTypePaper::class)->create([
                'bin_id' => $bin->id,
                'client_price_id' => $client_paper_price->id,
                'recycle_price_id' => $recyc_paper_price->id,
            ]);
            factory(BinTypeFabric::class)->create([
                'bin_id' => $bin->id,
                'client_price_id' => $client_fabric_price->id,
                'recycle_price_id' => $recyc_fabric_price->id,
            ]);

        }
    }
}
