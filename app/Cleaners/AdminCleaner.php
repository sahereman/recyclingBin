<?php

namespace App\Cleaners;


use Hhxsv5\LaravelS\Illuminate\Cleaners\CleanerInterface;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

class AdminCleaner implements CleanerInterface
{
    public function clean(Container $app, Container $snapshot)
    {
        \Encore\Admin\Admin::$script = [];
        \Encore\Admin\Admin::$deferredScript = [];
        \Encore\Admin\Admin::$headerJs = [];
        \Encore\Admin\Admin::$manifestData = [];
        \Encore\Admin\Admin::$extensions = [];

        $app->forgetInstance(\Encore\Admin\Admin::class);
        Facade::clearResolvedInstance(\Encore\Admin\Admin::class);
    }
}