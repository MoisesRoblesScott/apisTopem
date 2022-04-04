<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * se crea una nueva instancia de AuthController y se aplica la seguridad de middleware. al cargar el controlador
     * el construnctor es lo primero que carga.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Metodo de login. resibe como parametro dos valores.
     * el email y el password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [ //Validaciones
            'email' => 'required|string|email|max:100', //de que sea requerido, que sea un string, que sea tipo email y un maximo de 100 caracteres
            'password' => 'required|string|min:6', // de que sea requerido, que sea un string y como minimo 6 caracteres
        ]);

        if($validator->fails()){
            return ($validator->errors()->toJson()); // si la validacion no pasa aqui muestra los errores y los muestra en el front
        }
        
        $credenciales = request(['email', 'password']);

        if (! $token = auth()->attempt($credenciales)) { // si las credenciales son correctas esto me regresara el token
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token, $credenciales); //funcion que me regresa el token con la informacion del usuario
    }

    /**
     * Valida si el token aun esta vigente
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            return response()->json(auth()->user());
        } catch (\Throwable $th) {
            return response()->json($th);            
        }
    }

    /**
     * Metodo para cerrar sesion y eliminar el token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $userModel = new User();
        $userModel->limpiarToken(auth()->user()->email); //el token es almacenado en la DB en la tabla users al inicio de sesion. aqui lo que hace es actualizar el campo y dejarlo null
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refrescar el token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Metodo que retorna el token con la informacion del usuario.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $credenciales)
    {       
        $userModel = new User();
        $dataUsuario = $userModel->dataUsuario($credenciales, $token); //Busca la informacion del usuario y almacena el token en la DB

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'data_user' => $dataUsuario
        ]);
    }

    /* 
        Registrar usuarios
    */
    public function register(Request $request) 
    {   
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if($validator->fails()){
            return ($validator->errors()->toJson());
        }

        $user = User::create(array_merge(
            $validator->validate(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => '¡Usuario registrado exitosamente!',
            'user' => $user
        ], 201);
    }
}