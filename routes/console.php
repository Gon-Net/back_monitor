<?php


use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;


Schedule::command('test:hola')->everyTenSeconds();
Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
Schedule::command('app:migrate-mems')
    ->everyMinute()
    ->sendOutputTo(storage_path('logs/migrate-mems.log'))
    ->before(function () {
        \Log::info('Intentando ejecutar app:migrate-mems');
    })
    ->onFailure(function (Stringable $output) {
        \Log::error('Falló app:migrate-mems: '.$output);
    });;
*/
//Schedule::command('test:hola')->everyTwoSeconds();


