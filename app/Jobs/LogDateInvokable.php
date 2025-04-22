<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;

class LogDateInvokable
{
    public function __invoke()
    {
        Log::info(date('y-m-d H:i:s'));
    }
}