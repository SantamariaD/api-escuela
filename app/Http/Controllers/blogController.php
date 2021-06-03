<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//IMPORTAR MODELOS
use App\Blog;
use App\ContenidoBlog;

class blogController extends Controller
{
    //INICIO METODO PARA GUARDAR ARCHIVOS DE TEORIA EN EL SERVIDOR
    public function imagenBlog(Request $request,$nombreBlog,$idBlog)
    {
        //Recoger los datos que llegan
        $blog = $request->file('file0');
        //Validar que sea una blog
        $validador = \Validator::make($request->all(), [
            'file0' => 'required '
        ]);

        if (!$blog || $validador->fails()) {
            //error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'Hubo un error con el archivo quie intento subir.'
            );
        } else {
            //Crear carpeta del del blog donde se guardaran las imagenes
            \Storage::makeDirectory('blogs/' . $nombreBlog);
            //Traer el nombre de la blog agragando time en formato unix para que no se repitan nombres
            $archivo_nombre = time() .$blog->getClientOriginalName();
            //Subir el video a la carpeta de cursos
            \Storage::disk('blogs')->put($archivo_nombre, \File::get($blog));
            //Mover video a su carpeta correspondiente
            \Storage::move('blogs/' . $archivo_nombre, 'blogs/' . $nombreBlog . '/' . $archivo_nombre);
            //Actualizar campos en tablas
            Blog::where('id', $idBlog)->update(['imagen' => $archivo_nombre]);
            //Respuesta
            $respuesta = array(
                'status' => 'correcto',
                'mensaje' => 'El video se subio correctamente.',
                'imagen' => $archivo_nombre
            );
        }
        //Retornar respuesta
        return response()->json($respuesta);
    }
    //FIN METODO PARA GUARDAR ARCHIVOS DE TEORIA EN EL SERVIDOR

    // INICIO METODO PARA ACTUALIZAR UN BLOG
    public function actualizarBlog(Request $request, $id) {

        // Recibir datos por POST
        $json = $request->input('json', null);
        $parametros_array = json_decode($json, true);

        if(!empty($parametros_array)) {
            // No se actualiza

            // Actualizar registro
            Blog::where('id', $id)->update($parametros_array);

            // Mensaje de actualizacion
            $respuesta = array(
                'status' => 'correcto',
                'codigo' => 200,
                'mensaje' => 'Se actualizo correctamente la información.'
            );
        } else {
            //Mensaje de error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'No se actualizo correctamente la información.'
            );
        }

        //Retornar respuesta
        return response()->json($respuesta);
    }
    // FIN METODO PARA ACTUALIZAR UN BLOG

    // INICIO METODO PARA ACTUALIZAR UN BLOG
    public function actualizarContenidoBlog(Request $request, $id) {

        // Recibir datos por POST
        $json = $request->input('json', null);
        $parametros_array = json_decode($json, true);

        if(!empty($parametros_array)) {
            // No se actualiza

            // Actualizar registro
            ContenidoBlog::where('id', $id)->update($parametros_array);

            // Mensaje de actualizacion
            $respuesta = array(
                'status' => 'correcto',
                'codigo' => 200,
                'mensaje' => 'Se actualizo correctamente la información.'
            );
        } else {
            //Mensaje de error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'No se actualizo correctamente la información.'
            );
        }

        //Retornar respuesta
        return response()->json($respuesta);
    }
    // FIN METODO PARA ACTUALIZAR UN BLOG

    //INICIO METODO PARA GUARDAR UN BLOG
    public function guardarBlog(Request $request)
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
                'titulo' => 'required|unique:blogs',
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
                //Crear blog
                $blog = new Blog();
                $blog->titulo = $parametros_array['titulo'];
                $blog->usuario_id = $parametros_array['usuario_id'];
                //Guardar blog
                $blog->save();

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
    //FIN METODO PARA GUARDAR UN BLOG

    //INICIO METODO PARA GUARDAR UN CONTENIDO DE BLOG
    public function guardarContenidoBlog(Request $request)
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
                'id_blog' => 'required',
                'titulo_contenido' => 'required',
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
                //Crear blog
                $blog = new ContenidoBlog();
                $blog->id_blog = $parametros_array['id_blog'];
                $blog->titulo_contenido = $parametros_array['titulo_contenido'];
                $blog->contenido = $parametros_array['contenido'];
                $blog->imagen = $parametros_array['imagen'];
                //Guardar blog
                $blog->save();

                //Mensaje de respuesta
                $respuesta = array(
                    'status' => 'correcto',
                    'codigo' => 200,
                    'mensaje' => 'Se creo correctamente el blog.',
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
    //FIN METODO PARA GUARDAR UN CONTENIDO DE BLOG

    // INICIO METODO PARA ELIMINAR UN BLOG
    public function eliminarBlog($id) {
        // Conseguir blog con su id
        $blog = Blog::find($id);

        // Comprobamos que si se regreso un blog
        if ($blog) {
            // Se elimina
            $blog->delete();

            // mensaje de respuesta
            $respuesta = array(
                'status' => 'correcto',
                'codigo' => 200,
                'mensaje' => 'Se eliminó correctamente el blog'
            );
        } else {
            // mensaje de respuesta
            $respuesta = array(
                'status' => 'error',
                'codigo' => 500,
                'mensaje' => 'No existe el blog ingresado'
            );
        }

        // Retornar respuesta
        return response()->json($respuesta);
    }
    // FIN METODO PARA ELIMINAR UN BLOG

    // INICIO METODO PARA ELIMINAR EL CONTENIDO DEL BLOG
    public function eliminarContenidoBlog($id) {
        // Conseguir blog con su id
        $contenido = ContenidoBlog::find($id);

        // Comprobamos que si se regreso el contenido del blog
        if ($contenido) {
            // Se elimina
            $contenido->delete();

            // mensaje de respuesta
            $respuesta = array(
                'status' => 'correcto',
                'codigo' => 200,
                'mensaje' => 'Se eliminó correctamente el contenido de blog'
            );
        } else {
            // mensaje de respuesta
            $respuesta = array(
                'status' => 'error',
                'codigo' => 500,
                'mensaje' => 'No existe el contenido de blog ingresado'
            );
        }

        // Retornar respuesta
        return response()->json($respuesta);
    }
    // FIN METODO PARA ELIMINAR EL CONTENIDO DEL BLOG

    // INICIO METODO PARA TRAER TODOS LOS CONTENIDOS DE UN BLOG
    public function traerContenidoBlog(Request $request, $id) {
        $contenido = ContenidoBlog::where('id_blog', $id)->get();
        return response()->json($contenido);
    }
    // FIN METODO PARA TRAER TODOS LOS CURSOS DE UN ALUMNO

    //INICIO TRAER EL BLOG CON SU NOBRE
    public function traerBlog($nombre,$idBlog)
    {
        $blog = Blog::where('titulo', $nombre)
        ->where('usuario_id',$idBlog)
        ->get();
        return response()->json($blog);
    }
    //FIN TRAER EL BLOG CON SU NOBRE

}