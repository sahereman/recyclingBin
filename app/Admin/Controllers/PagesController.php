<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bin;
use App\Models\BinTypeFabric;
use App\Models\BinTypePaper;
use App\Models\ServiceSite;
use App\Models\User;
use App\Models\UserMoneyBill;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Echarts\Echarts;
use Illuminate\Support\Carbon;

class PagesController extends Controller
{

    public function index(Content $content)
    {
        /*return $content
            ->header('首页')
            ->description('数据统计')
            ->body(view('admin.pages.root'));*/

        $week = collect([
            Carbon::today()->subDays(6)->toDateString() => 0,
            Carbon::today()->subDays(5)->toDateString() => 0,
            Carbon::today()->subDays(4)->toDateString() => 0,
            Carbon::today()->subDays(3)->toDateString() => 0,
            Carbon::today()->subDays(2)->toDateString() => 0,
            Carbon::today()->subDays(1)->toDateString() => 0,
            Carbon::today()->toDateString() => 0,
        ]);

        // users
        $users = User::whereBetween('created_at', [Carbon::tomorrow()->subWeek(), Carbon::tomorrow()->subSecond()])->get();
        $grouped_user_counts = $users->groupBy(function ($item, $key) {
            return $item->created_at->toDateString();
        })->transform(function ($items) {
            return $items->count();
        });
        $grouped_user_counts = $week->merge($grouped_user_counts)->toArray();
        $data1 = [];
        foreach ($grouped_user_counts as $key => $value) {
            $data1[] = [
                'date' => $key,
                'count' => $value,
            ];
        }
        // bindData
        $header1 = [
            'date' => '日期',
            'count' => '用户人数',
        ];
        $echarts1 = (new Echarts())
            // ->setSeriesType('line')
            ->setSeries([
                ['type' => 'line', 'stack' => '用户人数'],
            ])
            // ->setShowShadow(true)
            ->setData($data1)
            ->bindLegend($header1)
            // ->setDataZoom(1)
            ->setShowToolbox(1);
        $box1 = new Box('近7天新增用户统计【新增用户共 ' . $users->count() . ' 人】', $echarts1);

        // fabric & paper
        $fabric = BinTypeFabric::all()->sum('number');
        $paper = BinTypePaper::all()->sum('number');
        $data2 = [
            ['name' => BinTypeFabric::NAME . '/kg', 'value' => number_format($fabric, 2, '.', '')],
            ['name' => BinTypePaper::NAME . '/kg', 'value' => number_format($paper, 2, '.', '')],
        ];
        $header2 = [
            'name' => '垃圾类型',
            'value' => '重量/kg'
        ];
        $echarts2 = (new Echarts())
            ->setSeriesType('pie')
            ->setSeries([
                ['type' => 'pie', 'name' => '垃圾类型'],
            ])
            // ->setShowShadow(true)
            ->setData($data2);
        // ->bindLegend($header1);
        $box2 = new Box('垃圾分类统计【垃圾总重 ' . number_format(($fabric + $paper), 2, '.', ',') . ' kg】', $echarts2);

        // client order money
        $client_order_money_collection = UserMoneyBill::where('type', 'clientOrder')->whereBetween('created_at', [Carbon::tomorrow()->subWeek(), Carbon::tomorrow()->subSecond()])->get();
        $grouped_client_order_money_collection = $client_order_money_collection->groupBy(function ($item, $key) {
            return $item->created_at->toDateString();
        })->transform(function ($items) {
            return $items->pluck('number')->sum();
        });
        $grouped_client_order_money_collection = $week->merge($grouped_client_order_money_collection)->toArray();
        $total_client_order_money = $client_order_money_collection->pluck('number')->sum();
        $data3 = [];
        foreach ($grouped_client_order_money_collection as $key => $value) {
            $data3[] = [
                'date' => $key,
                'number' => $value,
            ];
        }
        // bindData
        $header3 = [
            'date' => '日期',
            'number' => '支出金额',
        ];
        $echarts3 = (new Echarts())
            // ->setSeriesType('line')
            ->setSeries([
                ['type' => 'line', 'stack' => '支出金额'],
            ])
            // ->setShowShadow(true)
            ->setData($data3)
            ->bindLegend($header3)
            // ->setDataZoom(1)
            ->setShowToolbox(1);
        $box3 = new Box('近7天支出金额统计【支出金额共 ' . number_format($total_client_order_money, 2, '.', ',') . ' 元】', $echarts3);

        // user withdraw money
        $user_withdraw_money_collection = UserMoneyBill::where('type', 'userWithdraw')->whereBetween('created_at', [Carbon::tomorrow()->subWeek(), Carbon::tomorrow()->subSecond()])->get();
        $grouped_user_withdraw_money_collection = $user_withdraw_money_collection->groupBy(function ($item, $key) {
            return $item->created_at->toDateString();
        })->transform(function ($items) {
            return $items->pluck('number')->sum();
        });
        $grouped_user_withdraw_money_collection = $week->merge($grouped_user_withdraw_money_collection)->toArray();
        $total_user_withdraw_money = $user_withdraw_money_collection->pluck('number')->sum();
        $data4 = [];
        foreach ($grouped_user_withdraw_money_collection as $key => $value) {
            $data4[] = [
                'date' => $key,
                'number' => $value,
            ];
        }
        // bindData
        $header4 = [
            'date' => '日期',
            'number' => '提现金额',
        ];
        $echarts4 = (new Echarts())
            // ->setSeriesType('line')
            ->setSeries([
                ['type' => 'line', 'stack' => '提现金额'],
            ])
            // ->setShowShadow(true)
            ->setData($data4)
            ->bindLegend($header4)
            // ->setDataZoom(1)
            ->setShowToolbox(1);
        $box4 = new Box('近7天提现金额统计【提现金额共 ' . number_format($total_user_withdraw_money, 2, '.', ',') . ' 元】', $echarts4);

        $grouped_bin_ids = [];
        ServiceSite::all()->each(function (ServiceSite $serviceSite) use (&$grouped_bin_ids) {
            $grouped_bin_ids[$serviceSite->name] = $serviceSite->bins->pluck('id')->toArray();
        });

//        $data5 = [];
//        $fabric_number = 0;
//        foreach ($grouped_bin_ids as $site => $bin_ids) {
//            $fabric = BinTypeFabric::whereIn('bin_id', $bin_ids)->sum('number');
//            $fabric_number += $fabric;
//            $data5[] = ['name' => $site, 'value' => number_format($fabric, 2, '.', '')];
//        }
//        $header5 = [
//            'name' => '回收站点',
//            'value' => BinTypeFabric::NAME . '重量/kg'
//        ];
//        $echarts5 = (new Echarts())
//            ->setSeriesType('pie')
//            ->setSeries([
//                ['type' => 'pie', 'name' => '回收站点'],
//            ])
//            // ->setShowShadow(true)
//            ->setData($data5);
//        // ->bindLegend($header1);
//        $box5 = new Box(BinTypeFabric::NAME . '根据站点分类统计【总重 ' . number_format($fabric_number, 2, '.', ',') . ' kg】', $echarts5);
//
//        $data6 = [];
//        $paper_number = 0;
//        foreach ($grouped_bin_ids as $site => $bin_ids) {
//            $paper = BinTypePaper::whereIn('bin_id', $bin_ids)->sum('number');
//            $paper_number += $paper;
//            $data6[] = ['name' => $site, 'value' => number_format($paper, 2, '.', '')];
//        }
//        $header6 = [
//            'name' => '回收站点',
//            'value' => BinTypePaper::NAME . '重量/kg'
//        ];
//        $echarts6 = (new Echarts())
//            ->setSeriesType('pie')
//            ->setSeries([
//                ['type' => 'pie', 'name' => '回收站点'],
//            ])
//            // ->setShowShadow(true)
//            ->setData($data6);
//        // ->bindLegend($header1);
//        $box6 = new Box(BinTypePaper::NAME . '根据站点分类统计【总重 ' . number_format($paper_number, 2, '.', ',') . ' kg】', $echarts6);

        return $content
            ->header($title = '首页')
            ->description('数据统计')
            ->row(function (Row $row) use ($box1, $box2, $box3, $box4, $grouped_bin_ids) {
                $row->column(6, function (Column $column) use ($box1) {
                    $column->append($box1);
                });
                $row->column(6, function (Column $column) use ($box2) {
                    $column->append($box2);
                });
                $row->column(6, function (Column $column) use ($box3) {
                    $column->append($box3);
                });
                $row->column(6, function (Column $column) use ($box4) {
                    $column->append($box4);
                });
                // 站点分类统计
//                $row->column(6, function (Column $column) use ($box5) {
//                    $column->append($box5);
//                });
//                $row->column(6, function (Column $column) use ($box6) {
//                    $column->append($box6);
//                });
                // 垃圾箱分类统计
//                foreach ($grouped_bin_ids as $site => $bin_ids) {
//                    $data = [];
//                    foreach ($bin_ids as $bin_id) {
//                        $bin = Bin::find($bin_id);
//                        $data[] = [
//                            'name' => $bin->name,
//                            'fabric' => number_format($bin->type_fabric->number, 2, '.', ','),
//                            'paper' => number_format($bin->type_paper->number, 2, '.', ',')
//                        ];
//                    }
//                    $header = [
//                        'name' => '垃圾箱',
//                        'fabric' => BinTypeFabric::NAME . '重量/kg',
//                        'paper' => BinTypePaper::NAME . '重量/kg',
//                    ];
//                    $echarts = (new Echarts())
//                        ->setSeriesType('bar')
//                        /*->setSeries([
//                            ['type' => 'bar', 'name' => '垃圾箱'],
//                        ])*/
//                        // ->setShowShadow(true)
//                        ->setData($data)
//                        ->bindLegend($header)
//                        ->setDataZoom(1)
//                        ->setShowToolbox(1);
//                    $box = new Box($site . ' - 垃圾箱分类统计', $echarts);
//                    $row->column(6, function (Column $column) use ($box) {
//                        $column->append($box);
//                    });
//                }
            });
    }

    public function dashboard(Content $content)
    {
        return $content
            ->header('系统信息')
            ->description('信息')
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }

    public function horizon(Content $content)
    {
        return $content
            ->header('Horizon')
            ->description('Horizon')
            ->body('<iframe src="/horizon" width="100%" height="600px"></iframe>');
    }
}
