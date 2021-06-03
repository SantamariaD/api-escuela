<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//IMPORTAR MODELOS
use App\Comentario;

class comentariosController extends Controller
{
    
    //INICIO METODO PARA GUARDAR UN COMENTARIO
    public function guardarComentario(Request $request)
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
                'usuario_id' => 'required',
                'contenido' => 'required'
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
                //Crear comentario
                $comentario = new Comentario();
                $comentario->usuario_id = $parametros_array['usuario_id'];
                $comentario->contenido = $parametros_array['contenido'];
                //Guardar comentario
                $comentario->save();

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
    //INICIO METODO PARA GUARDAR VIDEO EN EL SERVIDOR

    //INICIO METODO PARA TRAER COMENTARIO
    public function traerComentarios(){
        //traer la información de la BD
        $comentario = Comentario::all();
        //Retornar la respuesta
        return response()->json($comentario);

    }
    //FIN METODO PARA TRAER COMENTARIO

    //INICIO METODO PARA BORRAR UN COMENTARIO
    public function borrarComentario($id){
        //traer el comentario a eliminar
        $comentario = Comentario::find($id);
        //Borrar comentario
        $comentario->delete();
        $respuesta = array(
            'status' => 'correcto',
            'mensaje' => 'se borro comentario'
        );
        return response()->json($respuesta);

    }
    //FIN 
}
