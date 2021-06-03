<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//IMPORTAR MODELOS
use App\Permiso;
use App\Comentario;
use App\Contacto;

class generalController extends Controller
{
    //INICIO METODO PARA TRAER LOS PERMISOS DE UN USUARIO
    public function traerPermisos($id)
    {
        //Traer los permisos de la BD
        $permisos = Permiso::where('usuario_id', $id)->get();
        //Regresar respuesta
        return response()->json($permisos);
    }
    //FIN METODO PARA TRAER LOS PERMISOS DE UN USUARIO

    //INICIO METODO PARA GUARDAR UN CONTACTO
    public function contacto(Request $request)
    {
        //Recibir datos
        $json = $request->input('json', null);
        $datos_objeto = json_decode($json);
        $datos_array = json_decode($json, true);

        if (!empty($datos_array && $datos_objeto)) {
            //Limpiar datos
            $parametros_array = array_map('trim', $datos_array);

            //Seleccionar los datos a validar
            $validador = \Validator::make($parametros_array, [
                'nombre' => 'required',
                'mensaje' => 'required',
                'email' => 'required'
            ]);
            //Validación
            if ($validador->fails()) {
                //Mensaje de error
                $respuesta = array(
                    'status' => 'error',
                    'codigo' => 500,
                    'mensaje' => 'Los datos no son correctos.',
                    'errores' => $validador->errors()
                );
            } else {
                //Crear contacto
                $contacto = new Contacto();
                $contacto->nombre = $parametros_array['nombre'];
                $contacto->mensaje = $parametros_array['mensaje'];
                $contacto->email = $parametros_array['email'];
                //Guardar contacto
                $contacto->save();

                //Mensaje de respuesta
                $respuesta = array(
                    'status' => 'correcto',
                    'codigo' => 200,
                    'mensaje' => 'Se creo correctamente el curso.',
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
    //FIN METODO PARA GUARDAR UN CONTACTO

}
