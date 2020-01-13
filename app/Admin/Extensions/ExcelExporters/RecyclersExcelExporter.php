<?php

namespace App\Admin\Extensions\ExcelExporters;

use App\Exceptions\SwooleExitException;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\ExcelExporter as BaseExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class RecyclersExcelExporter extends BaseExcelExporter implements WithMapping
{
    protected $fileName = '.xlsx';
    protected $headings = ['昵称', '手机号', '余额'];

    public function map($data): array
    {
        return [
            $data->name,
            $data->phone,
            $data->money,
        ];
    }

    public function export()
    {
        $this->fileName = $this->getTable() . '-' . now() . '.xlsx';

        $res = $this->download($this->fileName)->prepare(request());

        throw new SwooleExitException($res);
    }
}