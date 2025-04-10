<?php

namespace App\Http\Controllers;
use App\Models\TipoObservadorCategoria;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;

class TiposObservadoresCategoriaController extends Controller
{
    public function getAll(Request $request)
    {
        $perPage = $request->input('items', 100);
        return response()->json(ApiHelper::getAlloweds(TipoObservadorCategoria::class, $perPage), 200);
    }
}
