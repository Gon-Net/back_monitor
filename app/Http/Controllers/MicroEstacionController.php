<?php

namespace App\Http\Controllers;
use App\Models\MicroEstacion;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;

class MicroEstacionController extends Controller
{
    public function getAll(Request $request)
    {
        $perPage = $request->input('items', 100);
        return response()->json(ApiHelper::getAlloweds(MicroEstacion::class, $perPage), 200);
    }
}
