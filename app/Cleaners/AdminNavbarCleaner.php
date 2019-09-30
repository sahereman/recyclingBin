<?php

namespace App\Cleaners;


use Hhxsv5\LaravelS\Illuminate\Cleaners\CleanerInterface;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

class AdminNavbarCleaner implements CleanerInterface
{
    public function clean(Container $app, Container $snapshot)
    {

        info($app['admin']);
        info($app);
        
//        if (!$app->offsetExists('auth')) {
//            return;
//        }
//        $ref = new \ReflectionObject($app['auth']);
//        if ($ref->hasProperty('guards')) {
//            $guards = $ref->getProperty('guards');
//        } else {
//            $guards = $ref->getProperty('drivers');
//        }
//        $guards->setAccessible(true);
//        $guards->setValue($app['auth'], []);
//
//        $app->forgetInstance('auth.driver');
//        Facade::clearResolvedInstance('auth.driver');
    }
}