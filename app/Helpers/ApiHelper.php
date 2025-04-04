<?php

namespace App\Helpers;


class ApiHelper
{
    public static function getAlloweds($model, $perPage = 100, $all = false)
    {
        if ($all){
            return $model::where('estado', 'A')->get();
        }
        else {
            return $model::where('estado', 'A')->simplePaginate($perPage);
        }
    }
}
