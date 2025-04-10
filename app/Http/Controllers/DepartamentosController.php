<?php

namespace App\Http\Controllers;
use App\Models\Departamento;
use Illuminate\Http\Request;
use App\Helpers\ApiHelper;
use OpenApi\Annotations as OA;
class DepartamentosController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/departamentos",
     *     summary="Obtener departamentos",
     *     tags={"Departamentos"},
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function getAll(Request $request)
    {
        $perPage = $request->input('items', 100);
        return response()->json(ApiHelper::getAlloweds(Departamento::class, $perPage), 200);
    }
}
