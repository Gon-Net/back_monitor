<?php

namespace App\Console\Commands;

use App\Http\Controllers\MemController;
use Illuminate\Console\Command;
//php artisan app:migrate-mems
//php artisan schedule:work
class MigrateMems extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-mems';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all mems in one date';

    private function log_text($text)
    {
        file_put_contents(
            storage_path('logs/migrate-mems.log'),
            mb_convert_encoding($text. "\n", 'UTF-8'),
            FILE_APPEND
        );
    }
    /**
     * Execute the console command.
     */
    private function migrate($number_try = 1)
    {
        $controller = new MemController();
        $today = now();
        $count = 0;
        $this->log_text("Migración de MEMs ejecutada a las {$today}, intento $number_try/4\n");

        try{
            $date_migration = date('Y-m-d', strtotime('yesterday'));
            $count = $controller->migratePerDate($date_migration);
            $this->log_text("Migracion exitosa, se migro $count nuevos MEMs del dia $date_migration\n\n");
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
    public function handle()
    {
        $this->log_text("---------------------");
        $this->migrate(1);
    }
}
