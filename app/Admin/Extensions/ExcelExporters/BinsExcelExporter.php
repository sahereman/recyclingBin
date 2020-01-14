<?php

namespace App\Admin\Extensions\ExcelExporters;

use App\Exceptions\SwooleExitException;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\ExcelExporter as BaseExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class BinsExcelExporter extends BaseExcelExporter implements WithMapping
{
    protected $fileName = '.xlsx';
    protected $headings = ['智能箱编号', '站点名称', '箱体名称', '地址', '纺织物重量', '可回收物重量'];

    public function map($data): array
    {
        return [
            $data->no,
            data_get($data, 'site.name'),
            $data->name,
            $data->address,
            $data['types_snapshot']['type_fabric']['number'],
            $data['types_snapshot']['type_paper']['number'],
        ];
    }

    public function export()
    {
        $this->fileName = $this->getTable() . '-' . now() . '.xlsx';

        $res = $this->download($this->fileName)->prepare(request());

        throw new SwooleExitException($res);
    }
}