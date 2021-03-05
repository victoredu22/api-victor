<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Alumno;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Response;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->User = new User();
        $this->Alumno = new Alumno();
    }

    public function imagenLogin()
    {
        $public = public_path();
        $url = $public.'\storage'.'\imgInicio.jpg';

        return response()->json([
            'ok'=>true,
            'url'=>$url
        ]);
    }
    /**
     * Metodo que verfica las credenciales segun los parametros
     *
     * @param numeroDocumento,password
     */
    public function login(Request $request)
    {
        $credentials = request(['numeroDocumento', 'password']);
        $jwt_token = null;

        if (!$jwt_token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario Incorrecto',
            ], 400);
        }

        $token = $this->respondWithToken($jwt_token);

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numeroDocumento' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);
        
        
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        
        $usuarioNuevo = $this->User->createUser($request);
    
        $token = JWTAuth::fromUser($usuarioNuevo);

        return response()->json(compact('usuarioNuevo', 'token'), 201);
    }
    public function getUsuario(Request $request)
    {
        dd("hola");
    }


    public function getAuthUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        $user = JWTAuth::authenticate($request->token);
 
        return response()->json(['user' => $user]);
    }

    public function loginLogeado()
    {
        $infoAlumno = $this->Alumno->getAlumnosById(auth()->user()->idAlumno);

  
        return response()->json([
            'ok'=>true,
            'usuarioDB'=>$infoAlumno
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }
}
