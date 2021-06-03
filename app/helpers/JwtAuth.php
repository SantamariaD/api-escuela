<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use illuminate\Support\Facades\DB;
use App\Usuario;

class JwtAuth{

    //VARIABLES
    public $key;

    public function __construct()
    {
        //Clave de cifrado de contraseñas
        $this->key = 'super_clave_mamalona_1234_jajaja';
    }

    //INICIO METODO INICIO DE SESIÓN
    public function iniciarSesion($email, $contrasena, $getToken=null){
        //Buscar si existe usuario con las credenciales solicitadas
        $usuario = Usuario::where([
            'email' => $email,
            'contrasena' => $contrasena
        ])->first();

        //Comprobar si son correctos
        $signup = false;
        if(is_object($usuario)){
            $signup = true;
        }

        //Generar el token con los datos del usuaio identificado
        if($signup){
            $token = array (
                'sub'       =>        $usuario->id,
                'email'  =>        $usuario->email,
                'nombre' =>         $usuario->nombre,
                'usuario' =>         $usuario->usuario,
                'iat'       =>        time(),
                'exp'       =>        time()+ (60*60*24) 
            );
        
            $jwt = JWT::encode($token, $this->key, 'HS256');
            //Decodificación del token y devuelve la información del usuario
            $decode = JWT::decode($jwt, $this->key, ['HS256']);

            //Devolver los datos decodificados o el token, en funcion de un parametro
            if (is_null($getToken)) {
                $data = $jwt;
            }else{
                $data = $decode;
            }
        }else{
            $data = array(
                'status' => 'error',
                'mensaje' => 'Login incorrecto.'
                
            );
        }

        return $data;
    }
    //FIN METODO INICIO DE SESIÓN

    //INICIO METODO CHECKTOKEN
    public function checkToken($token, $getIdentity = false){
        $auth = false;

        //Capturar errores 
        try {
            $token = str_replace('"', '', $token);
            $decoded = JWT::decode($token, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = true;
        } catch(\DomainException $e){
            $auth = true;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        }else{
            $auth = false;
        }

        if ($getIdentity) {
            return $decoded;
        }

        return $auth;

    }
    //FIN METODO CHECKTOKEN

}
