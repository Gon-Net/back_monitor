<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LogDateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:log-date-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle():int
    {
        \Log::info(date("y-m-d H:i:s"));
        return 0;
    }
}
