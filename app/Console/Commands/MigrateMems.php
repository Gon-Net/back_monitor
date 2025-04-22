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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $controller = new MemController();
        $today = now();
        $count = 0;
        echo "Migración de MEMs ejecutada a las {$today}";
        try{
            $count = $controller->migratePerDate(date('Y-m-d', strtotime('yesterday')));
            echo "Se migraron nuevos {$count} MEMs";
        }
        catch (\Exception $e) {
            echo 'Error al migrar';
            echo $e->getMessage();
        }
    }
}
