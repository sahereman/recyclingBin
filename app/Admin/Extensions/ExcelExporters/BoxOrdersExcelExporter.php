<?php

namespace App\Admin\Extensions\ExcelExporters;

use App\Exceptions\SwooleExitException;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\ExcelExporter as BaseExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class BoxOrdersExcelExporter extends BaseExcelExporter implements WithMapping
{
    protected $fileName = '.xlsx';
    protected $headings = ['投递时间', '传统箱名称', '用户名', '状态', '奖励金', '订单号', '图片凭证链接地址'];

    public function map($data): array
    {
        return [
            $data->created_at,
            data_get($data, 'box.name'),
            data_get($data, 'user.name'),
            $data->status_text,
            $data->total,
            $data->sn,
            $data->image_proof_url,
        ];
    }

    public function export()
    {
        $this->fileName = $this->getTable() . '-' . now() . '.xlsx';

        $res = $this->download($this->fileName)->prepare(request());

        throw new SwooleExitException($res);
    }
}