<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestHola extends Command
{
    protected $signature = 'test:hola';

    protected $description = 'Comando de prueba';

    public function handle()
    {
        $date = now();
        \Log::info('Comando test:hola ejecutado correctamente');
        echo "Hola desde el comando".$date."\n";
    }
}
