<?php

namespace App\Admin\Extensions\ExcelExporters;

use App\Exceptions\SwooleExitException;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\ExcelExporter as BaseExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class BinsExcelExporter extends BaseExcelExporter implements WithMapping
{
    protected $fileName = '.xlsx';
    protected $headings = ['智能箱编号', '站点名称', '箱体名称', '地址', '可回收物', '纺织物'];

    public function map($data): array
    {
        return [
            $data->no,
            data_get($data, 'site.name'),
            $data->name,
            $data->address,
            // 可回收物
            '状态:' . $data['types_snapshot']['type_paper']['status_text'] . ' ' .
            '数量:' . $data['types_snapshot']['type_paper']['number'] . ' ' .
            '投递价格:' . $data['types_snapshot']['type_paper']['client_price']['price'] . ' ' .
            '回收价格:' . $data['types_snapshot']['type_paper']['clean_price']['price'],
            // 纺织物
            '状态:' . $data['types_snapshot']['type_fabric']['status_text'] . ' ' .
            '数量:' . $data['types_snapshot']['type_fabric']['number'] . ' ' .
            '投递价格:' . $data['types_snapshot']['type_fabric']['client_price']['price'] . ' ' .
            '回收价格:' . $data['types_snapshot']['type_fabric']['clean_price']['price'],

        ];
    }

    public function export()
    {
        $this->fileName = $this->getTable() . '-' . now() . '.xlsx';

        $res = $this->download($this->fileName)->prepare(request());

        throw new SwooleExitException($res);
    }
}