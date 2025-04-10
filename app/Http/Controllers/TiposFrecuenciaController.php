<?php

namespace App\Http\Controllers;
use App\Models\TipoFrecuencia;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;

class TiposFrecuenciaController extends Controller
{
    public function getAll(Request $request)
    {
        $perPage = $request->input('items', 100);
        return response()->json(ApiHelper::getAlloweds(TipoFrecuencia::class, $perPage), 200);
    }
}
