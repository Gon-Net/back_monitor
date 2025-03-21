<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class ApiHelper
{
    public static function getAlloweds($model)
    {
        return $model::where('estado', 'A')->get();
    }
}
