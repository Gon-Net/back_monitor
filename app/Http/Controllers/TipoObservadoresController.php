<?php

namespace App\Http\Controllers;
use App\Models\TipoObservador;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;

class TipoObservadoresController extends Controller
{
    public function getAll(Request $request)
    {
        $perPage = $request->input('items', 100);
        return response()->json(ApiHelper::getAlloweds(TipoObservador::class, $perPage), 200);
    }
}
