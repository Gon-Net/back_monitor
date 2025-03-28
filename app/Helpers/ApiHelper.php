<?php

namespace App\Helpers;


class ApiHelper
{
    public static function getAlloweds($model, $perPage = 100)
    {
        return $model::where('estado', 'A')->simplePaginate($perPage);
    }
}
