<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
#php artisan l5-swagger:generate
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/login",
     *     summary="Login the app",
     *     tags={"Login"},
     *     @OA\Response(
     *         response=201,
     *         description="Success"
     *     )
     * )
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'nombre_usuario' => 'required',
                'numero_documento_identidad' => 'required',
            ]);

            $user = \App\Models\ObservadorAuth::where('nombre_usuario', $credentials['nombre_usuario'])
                ->where('numero_documento_identidad', $credentials['numero_documento_identidad'])
                ->first();

            if ($user) {
                $token = $user->createToken('token-name');
                //1 semana en dias
                $token->accessToken->expires_at = now()->addDays(7);
                $token->accessToken->save();
                return response()->json(['token' => $token->plainTextToken], 200);
            }

            return response()->json(['message' => 'Credenciales inválidas'], 401);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Datos inválidos.',
                'errors' => $e->errors(),
            ], 404);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Usuario registrado exitosamente',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Datos inválidos.',
                'errors' => $e->errors(),
            ], 404);
        }
    }
}


/*

*/