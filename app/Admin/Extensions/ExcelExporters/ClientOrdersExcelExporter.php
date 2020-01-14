<?php

namespace App\Admin\Extensions\ExcelExporters;

use App\Exceptions\SwooleExitException;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\ExcelExporter as BaseExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientOrdersExcelExporter extends BaseExcelExporter implements WithMapping
{
    protected $fileName = '.xlsx';
    protected $headings = ['投递时间', '智能箱编号', '智能箱名称', '用户名',
        '状态', '合计金额', '订单号', '纺织物重量', '纺织物金额', '可回收物重量', '可回收物金额'];

    public function map($data): array
    {
        return [
            $data->created_at,
            data_get($data, 'bin.no'),
            data_get($data, 'bin.name'),
            data_get($data, 'user.name'),
            $data->status_text,
            $data->total,
            $data->sn,
            $data->items->where('type_slug', 'fabric')->sum('number'),
            $data->items->where('type_slug', 'fabric')->sum('subtotal'),
            $data->items->where('type_slug', 'paper')->sum('number'),
            $data->items->where('type_slug', 'paper')->sum('subtotal'),
        ];
    }

    public function export()
    {
        $this->fileName = $this->getTable() . '-' . now() . '.xlsx';

        $res = $this->download($this->fileName)->prepare(request());

        throw new SwooleExitException($res);
    }
}