<?php

namespace App\Admin\Extensions\ExcelExporters;

use App\Exceptions\SwooleExitException;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\ExcelExporter as BaseExcelExporter;
use function GuzzleHttp\Psr7\str;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExcelExporter extends BaseExcelExporter implements WithMapping
{
    protected $fileName = '.xlsx';
    protected $headings = ['昵称', '性别', '手机号', '余额', '累计次数', '累计重量', '累计金额', '注册时间', '实名认证'];

    public function map($data): array
    {
        return [
            $data->name,
            $data->gender,
            $data->phone,
            $data->money,
            $data->total_client_order_count,
            $data->total_client_order_number,
            $data->total_client_order_money,
            $data->created_at,
            $data->real_authenticated_at ? '是' : '否',
        ];
    }

    public function export()
    {
        $this->fileName = $this->getTable() . '-' . now() . '.xlsx';

        $res = $this->download($this->fileName)->prepare(request());

        throw new SwooleExitException($res);
    }
}