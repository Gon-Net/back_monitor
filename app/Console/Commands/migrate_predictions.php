<?php

namespace App\Console\Commands;

use App\Http\Controllers\PrediccionController;
use Illuminate\Console\Command;

class migrate_predictions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate_predictions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra las predicciones a la base de datos';


    private function log_text($text)
    {
        file_put_contents(
            storage_path('logs/migrate-forecasts.log'),
            mb_convert_encoding($text. "\n", 'UTF-8'),
            FILE_APPEND
        );
    }
    
    public function migrate($number_try = 1)
    {
        $controller = new PrediccionController();
        $today = now();
        $count = 0;
        $this->log_text("Migración de pronosticos ejecutada a las {$today}, intento $number_try/4\n");

        try{
            $count = $controller->migrateForecasts();
            $this->log_text("Migracion exitosa, se migro $count nuevos pronosticos.\n");
            return true;
        }
        catch (\Exception $e) {
            $this->log_text("Error al migrar \n");
            $this->log_text($e->getMessage());
            if ($number_try === 4)
            {
                echo "No se pudo realizar la migracion \n";
                return false;
            }
            else
            {
                return $this->migrate($number_try + 1);
            }
        }
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->log_text("---------------------");
        $this->migrate(1);
    }
}
