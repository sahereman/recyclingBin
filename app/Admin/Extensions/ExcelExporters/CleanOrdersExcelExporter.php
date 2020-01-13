<?php

namespace App\Admin\Extensions\ExcelExporters;

use App\Exceptions\SwooleExitException;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\ExcelExporter as BaseExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class CleanOrdersExcelExporter extends BaseExcelExporter implements WithMapping
{
    protected $fileName = '.xlsx';
    protected $headings = ['回收时间', '智能箱编号', '智能箱名称', '回收员',
        '状态', '合计金额', '订单号', '回收物详情'];

    public function map($data): array
    {
        $items_str = '';
        foreach ($data->items as $item)
        {
            $items_str .= "$item[type_name]:$item[number]$item[unit] / $item[subtotal]元  ";
        }
        return [
            $data->created_at,
            data_get($data, 'bin.no'),
            data_get($data, 'bin.name'),
            data_get($data, 'recycler.name'),
            $data->status_text,
            $data->total,
            $data->sn,
            $items_str
        ];
    }

    public function export()
    {
        $this->fileName = $this->getTable() . '-' . now() . '.xlsx';

        $res = $this->download($this->fileName)->prepare(request());

        throw new SwooleExitException($res);
    }
}