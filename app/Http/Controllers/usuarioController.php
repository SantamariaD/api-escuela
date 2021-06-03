<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//IMPORTAR MODELOS
use App\Usuario;
use App\Permiso;
use App\JoinCursoUsuario;

class usuarioController extends Controller
{
    //INICIO METODO PARA REGISTRAR ALUMNO
    public function registrarAlumno(Request $request)
    {
        //Recibir datos
        $json = $request->input('json', null);
        $datos_objeto = json_decode($json);
        $datos_array = json_decode($json, true);

        if (!empty($datos_array && $datos_objeto)) {
            //Limpiar datos
            $parametros_array = array_map('trim', $datos_array);

            //Validar datos
            $validador = \Validator::make($parametros_array, [
                'email' => 'required|email|unique:usuarios',
                'contrasena' => 'required',
                'nombre' => 'required',
                'usuario' => 'required|unique:usuarios'
            ]);

            if ($validador->fails()) {
                //Mensaje de error
                $respuesta = array(
                    'status' => 'error',
                    'codigo' => 500,
                    'mensaje' => 'Los datos no son correctos.',
                    'errores' => $validador->errors()
                );
            } else {
                //Encriptar contraseña
                $contra = hash('sha256', $datos_objeto->contrasena);

                //Crear usuario
                $usuario = new Usuario();
                $usuario->nombre = $parametros_array['nombre'];
                $usuario->email = $parametros_array['email'];
                $usuario->usuario ='Ω'.$parametros_array['usuario'];
                $usuario->contrasena = $contra;

                //Guardar usuario
                $usuario->save();

                //Mensaje de respuesta
                $respuesta = array(
                    'status' => 'correcto',
                    'codigo' => 200,
                    'mensaje' => 'Registro de alumno correcto.',
                );
            }
        } else {
            //Mensaje de error
            $respuesta = array(
                'status' => 'error',
                'codigo' => 500,
                'mensaje' => 'Informacion no valida.',
            );
        }

        //Retorno de respuesta en json
        return response()->json($respuesta);
    }
    //FIN METODO PARA REGISTRAR ALUMNO

    //INICIO METODO PARA REGISTRAR ALUMNO
    public function registrarProfesor(Request $request)
    {
        //Recibir datos
        $json = $request->input('json', null);
        $datos_objeto = json_decode($json);
        $datos_array = json_decode($json, true);

        if (!empty($datos_array && $datos_objeto)) {
            //Limpiar datos
            $parametros_array = array_map('trim', $datos_array);

            //Validar datos
            $validador = \Validator::make($parametros_array, [
                'usuario_id' => 'required',
                'tipo' => 'required',
                'email' => 'required',
                'contrasena' => 'required'
            ]);

            if ($validador->fails()) {
                //Mensaje de error
                $respuesta = array(
                    'status' => 'error',
                    'codigo' => 500,
                    'mensaje' => 'Los datos no son correctos.',
                    'errores' => $validador->errors()
                );
            } else {
                //Pasar parametros a provider JwtAuth
                $email = $datos_objeto->email;
                $contrasena = $parametros_array['contrasena'];
                $pwd = hash('sha256', $contrasena);

                //Buscar si existe usuario con las credenciales solicitadas
                $usuario = Usuario::where([
                    'email' => $email,
                    'contrasena' => $pwd
                ])->first();

                //Comprobar si son correctos
                if (is_object($usuario)) {
                    //Crear permiso
                    $permiso = new Permiso();
                    $permiso->usuario_id = $parametros_array['usuario_id'];
                    $permiso->tipo = $parametros_array['tipo'];

                    //Guardar permiso
                    $permiso->save();

                    //Mensaje de respuesta
                    $respuesta = array(
                        'status' => 'correcto',
                        'codigo' => 200,
                        'mensaje' => 'Registro de permisos correcto.',
                    );
                } else {
                    //Mensaje de error
                    $respuesta = array(
                        'status' => 'error',
                        'codigo' => 500,
                        'mensaje' => 'No coinciden la contraseña o email.',
                    );
                }
            }
        } else {
            //Mensaje de error
            $respuesta = array(
                'status' => 'error',
                'codigo' => 500,
                'mensaje' => 'Informacion no valida.',
            );
        }

        //Retorno de respuesta en json
        return response()->json($respuesta);
    }
    //FIN METODO PARA REGISTRAR ALUMNO

    //INICIO METODO PARA LOGIN
    public function login(Request $request)
    {
        $jwtAuth = new \JwtAuth();

        //INICIO RECIBIR INFORMACIÓN POR POST
        $json = $request->input('json', null);
        $datos_objeto = json_decode($json);
        $datos_array = json_decode($json, true);
        //INICIO RECIBIR INFORMACIÓN POR POST

        //INICIO VALIDAR QUE LOS DATOS NO ESTAN VACIOS
        if (!empty($datos_array && $datos_objeto)) {
            //Limpiar datos
            $parametros_array = array_map('trim', $datos_array);

            //Validar datos
            $validador = \Validator::make($parametros_array, [
                'email' => 'required',
                'contrasena' => 'required'
            ]);

            if ($validador->fails()) {
                //Mensaje de error
                $respuesta = array(
                    'status' => 'error',
                    'codigo' => 500,
                    'mensaje' => 'Los datos no son correctos.',
                    'errores' => $validador->errors()
                );
            } else {
                //Pasar parametros a provider JwtAuth
                $email = $datos_objeto->email;
                $contrasena = $parametros_array['contrasena'];
                $pwd = hash('sha256', $contrasena);
                //Se manda a provider JwtAuth info para recibir token del usuario
                $resp = response()->json($jwtAuth->iniciarSesion($email, $pwd));
                $respuesta = $resp->original;

                if (!empty($datos_objeto->gettoken)) {
                    //Se manda a provider JwtAuth info para recibir información del usuario
                    $resp = response()->json($jwtAuth->iniciarSesion($email, $pwd, true));
                    $respuesta = $resp->original;
                }
            }
        } else {
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'No se ingresaron los datos.'
            );
        }

        return response()->json($respuesta);
    }
    //FIN METODO PARA LOGIN

    //INICIO METODO PARA GUARDAR INFORMACIÓN PERSONAL DEL ALUMNO
    public function informacionAlumno(Request $request)
    {
        //INICIO COMPROBAR LOGUEO
        //Obtner token de login
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        //Comprobar que este logueado
        if ($checkToken) {
            //Recoger parametros por post
            $json = $request->input('json', null);
            $parametros_array = json_decode($json, true);
            var_dump($parametros_array);
            die();

            if (!empty($parametros_array)) {
                //Actualizar campos
                $actualizar = Usuario::where('id', $parametros_array['id'])->update($parametros_array);

                //Mensaje de atcualización
                $respuesta = array(
                    'status' => 'correcto',
                    'mensaje' => 'Se actualizo correctamente la información.'
                );
            } else {
                //Mensaje de error
                $respuesta = array(
                    'status' => 'error',
                    'mensaje' => 'No se actualizo correctamente la información.'
                );
            }
        } else {
            //Mensaje de error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'No ha iniciado sesión,.'
            );
        }
        //FIN COMPROBAR LOGUEO



        //Devolver respuesta
        return response()->json($respuesta);
    }
    //FIN METODO PARA GUARDAR INFORMACIÓN PERSONAL DEL ALUMNO

    //INICIO METODO PARA TRAER INFORMACION DEL ALUMNO
    public function infoAlumno(Request $request, $id)
    {
        //INICIO COMPROBAR LOGUEO
        //Obtner token de login
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        //Comprobar que este logueado
        if ($checkToken) {
            //Traer cambios generales de la base de datos
            $cambios =  Usuario::where('id', $id)->get();
            //Devolver contactos
            return response()->json($cambios);
        } else {
            echo ' No logueado';
        }
        //FIN COMPROBAR LOGUEO

    }
    //FIN METODO PARA TRAER INFORMACION DEL ALUMNO

    //INICIO METODO DE AJUSTES DE INFORMACION ALUMNO
    public function ajusteInfoAlumno(Request $request, $id)
    {
        //Recoger parametros por post
        $json = $request->input('json', null);
        $parametros_array = json_decode($json, true);

        if (!empty($parametros_array)) {
            //No se actualiza
            unset($parametros_array['id']);
            unset($parametros_array['contrasena']);
            
            //Actualizar campos
            $actualizar = Usuario::where('id', $id)->update($parametros_array);

            //Mensaje de atcualización
            $respuesta = array(
                'status' => 'correcto',
                'mensaje' => 'Se actualizo correctamente la información.'
            );
        } else {
            //Mensaje de error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'No se actualizo correctamente la información.'
            );
        }

        //Devolver respuesta
        return response()->json($respuesta);
    }
    //FIN METODO DE AJUSTES DE INFORMACION ALUMNO

    // INICIO METODO PARA REGISTRAR UN USUARIO A UN CURSO
    public function registrarseACurso(Request $request) {
        // Recoger parametros por post
        $json = $request->input('json', null);
        $parametros_array = json_decode($json, true);   // parametros como arreglo
        $parametros_objeto = json_decode($json);       // parametros como objeto

        // Checamos que los parametros no estén vacíos
        if(!empty($parametros_array)) {
            
            //Validar datos
            $validador = \Validator::make($parametros_array, [
                'id_usuario' => 'required',
                'id_curso' => 'required',
                'nombre_usuario' => 'required',
                'nombre_curso' => 'required'
            ]);
            if ($validador->fails()) {
                // Mensaje de respuesta
                $respuesta = array(
                    'status' => 'error',
                    'codigo' => 500,
                    'mensaje' => 'Los parametros no estan completos'
                );
            } else {
                // Crear objeto
                $curso_usuario = new JoinCursoUsuario();
                $curso_usuario->id_usuario = $parametros_array['id_usuario'];
                $curso_usuario->id_curso = $parametros_array['id_curso'];
                $curso_usuario->nombre_usuario = $parametros_array['nombre_usuario'];
                $curso_usuario->nombre_curso = $parametros_array['nombre_curso'];

                // Guardar registro en la base
                $curso_usuario->save();

                // Mensaje de respuesta
                $respuesta = array(
                    'status' => 'correcto',
                    'codigo' => 200,
                    'mensaje' => 'Registrado correctamente al curso'
                );
            }
        } else {
            // Mensaje de respuesta
            $respuesta = array(
                'status' => 'error',
                'codigo' => 500,
                'mensaje' => 'Los parametros estan vacios'
            );
        }

        return response()->json($respuesta);
    }
    // FIN METODO PARA REGISTRAR UN USUARIO A UN CURSO

    // INICIO METODO PARA TRAER TODOS LOS CURSOS DE UN ALUMNO
    public function traerCursosAlumno(Request $request, $id) {
        
        $cursos = JoinCursoUsuario::where('id_usuario', $id)->get();
        return response()->json($cursos);
    }
    // FINMETODO PARA TRAER TODOS LOS CURSOS DE UN ALUMNO

}
