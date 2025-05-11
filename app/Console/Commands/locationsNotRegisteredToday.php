<?php

namespace App\Console\Commands;

use App\Http\Controllers\PrecipitacionController;
use Illuminate\Console\Command;

class locationsNotRegisteredToday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:locations-not-registered-today';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the locations not registered today';
    private function log_text($text)
    {
        file_put_contents(
            storage_path('logs/not-registered-today.log'),
            mb_convert_encoding($text. "\n", 'UTF-8'),
            FILE_APPEND
        );
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
            
        try{
            $controller = new PrecipitacionController();
            $today = now();
            $this->log_text("Obtencion de ubicaciones no registrados en fecha {$today}.\n");
            $this->log_text($controller->getAusentUbicationsPerDate($today->format("Y-m-d"))."\n");
            $this->log_text("---------\n");
        }
        catch (\Exception $e) {
            $this->log_text("Error al obtener las ubicaciones faltantes.\n");
            $this->log_text($e->getMessage());
        }
    }
}
