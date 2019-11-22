<?php

namespace App\Admin\Extensions\ExcelExporters;

use App\Exceptions\SwooleExitException;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\ExcelExporter as BaseExcelExporter;

class ExcelExporter extends BaseExcelExporter
{
    protected $fileName = '.xlsx';


    public function export()
    {
        $this->fileName = $this->getTable() . '-' . now() . '.xlsx';

        $res = $this->download($this->fileName)->prepare(request());

        throw new SwooleExitException($res);
    }
}