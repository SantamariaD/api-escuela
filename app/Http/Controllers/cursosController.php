<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Area;
use App\Carrera;
use App\Video;
use App\Curso;
use App\Tema;
use App\JoinCursoCarrera;
use App\JoinCursoProfesor;
use App\JoinCursoUsuario;
use App\Teoria;

/*
La clase "cursosController" tiene todos los métodos relacionados a los cursos los cuales incluyen ingreso de datos de curso, contenido, guardar video, guardar titulos de videos, etc. También se incluye la creación tanto de areas como de carreras
*/

class cursosController extends Controller
{
    //INICIO METODO PARA TRAER TODOS LOS CURSOS
    public function traerCursos()
    {
        $curso = Curso::all()->orderBy('nombre', 'ASC')->get();
        return response()->json($curso);
    }
    //FIN METODO PARA TRAER TODOS LOS CURSOS

    //INICIO METODO PARA TRAER UN SOLA CARRERA
    public function carrera($nombre)
    {
        //Buscar si existe curso con el nombre del curso
        $carrera = Carrera::where([
            'nombre' => $nombre
        ])->first();

        return  response()->json($carrera);
    }
    //FIN METODO PARA TRAER UN SOLA CARRERA

    //INICIO METODO PARA CREAR UN CURSO
    public function crearCurso(Request $request)
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
                'nombre' => 'required|unique:cursos',
                'descripcion' => 'required',
                'profesor_id' => 'required',
                'duracion' => 'required'
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
                //Crear curso
                $curso = new Curso();
                $curso->nombre = $parametros_array['nombre'];
                $curso->imagen = $parametros_array['imagen'];
                $curso->profesor_id = $parametros_array['profesor_id'];
                $curso->duracion = $parametros_array['duracion'];
                $curso->descripcion = $parametros_array['descripcion'];
                $curso->calificacion = $parametros_array['calificacion'];
                $curso->area_id = $parametros_array['area'];
                //Guardar curso
                $curso->save();
                //Limpiar nombre de video
                $nomLimpio = str_replace(['¿', '?', '!', '¡', '"', '\''], '', $parametros_array['nombre']);
                //Crear carpeta del curso donse se guardaran los videos storage/app/cursos/*
                \Storage::makeDirectory('cursos/' . $nomLimpio);

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
    //FIN METODO PARA CREAR UN CURSO

    //INICIO METODO PARA TRAER UN CURSO CON SU ID
    public function unCursoId($id)
    {
        $curso = Curso::where('id_curso', $id)->get();
        return response()->json($curso);
    }
    //FIN METODO PARA TRAER UN CURSO CON SU ID

    //INICIO METODO PARA GUARDAR UN CURSO CON UN PROFESOR
    public function guardarCursoProfesor(Request $request)
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
                'curso_id' => 'required',
                'area_id' => 'required',
                'profesor_id' => 'required',
                'profesor' => 'required'
            ]);
            //Validación
            if ($validador->fails()) {
                //Mensaje de error
                $respuesta = array(
                    'status' => 'error',
                    'codigo' => 500,
                    'mensaje' => 'Los datos no son correctos.',
                    'errores' => $validador->errors(),
                    'parametros' => $parametros_array
                );
            } else {
                //Crear joincursoprofesor
                $joinCursoProf = new JoinCursoProfesor();
                $joinCursoProf->curso_id = $parametros_array['curso_id'];
                $joinCursoProf->area_id = $parametros_array['area_id'];
                $joinCursoProf->profesor_id = $parametros_array['profesor_id'];
                $joinCursoProf->profesor = $parametros_array['profesor'];
                //Guardar joinCursoProf
                $joinCursoProf->save();

                //Mensaje de respuesta
                $respuesta = array(
                    'status' => 'correcto',
                    'codigo' => 200,
                    'mensaje' => 'Se enlazo el curso con el profesor correctamente.',
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
    //FIN METODO PARA GUARDAR UN CURSO CON UN PROFESOR

    //INICIO METODO PARA TRAER UN CURSO DE UN PROFESOR
    public function unCursoProf($idProf, $nombre)
    {
        $curso = Curso::where('nombre', $nombre)
            ->where('profesor_id', $idProf)
            ->get();
        return  response()->json($curso);
    }
    //FIN METODO PARA TRAER UN CURSO DE UN PROFESOR

    //INICIO METODO PARA TRAER LOS CURSOS DE UN PROFESOR
    public function cursosProfesor($id)
    {
        $cursos = \DB::table('join_curso_profesores')
            ->join('cursos', 'join_curso_profesores.curso_id', '=', 'cursos.id_curso')
            ->select('join_curso_profesores.curso_id', 'join_curso_profesores.profesor_id', 'join_curso_profesores.area_id', 'cursos.nombre', 'cursos.imagen', 'cursos.duracion')
            ->where('join_curso_profesores.profesor_id', $id)
            ->get();
        //Retornar respuesta
        return response()->json($cursos);
    }
    //INICIO METODO PARA TRAER LOS CURSOS DE UN PROFESOR

    //INICIO METODO PARA TRAER A LOS PROFESORES QUE CONTIENE UN AREA
    public function profesoresArea($id)
    {
        $profesores = JoinCursoProfesor::where('area_id', $id)
            ->distinct()
            ->get(['profesor_id', 'profesor']);
        //Retornar respuesta
        return response()->json($profesores);
    }
    //FIN METODO PARA TRAER A LOS PROFESORES QUE CONTIENE UN AREA

    //INICIO METODO PARA GUARDAR VIDEO EN EL SERVIDOR
    public function guardarVideo(Request $request, $id, $videoNombre)
    {
        //Recoger los datos que llegan
        $video = $request->file('file0');
        //Validar que sea una video
        $validador = \Validator::make($request->all(), [
            'file0' => 'required '
        ]);

        if (!$video || $validador->fails()) {
            //error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'Las extenciones que se aceptan son: mp4, mpeg-4, avi, flv, mov y wmv.'
            );
        } else {
            //Traer el nombre de la video agragando time en formato unix para que no se repitan nombres
            $video_nombre = time() . $video->getClientOriginalName();
            //Traer el nombre del curso para crear una carpeta
            $curso = Curso::where('id_curso', $id)->get();
            $jsonCurso = json_encode($curso);
            //Convertir a array
            $nombreCurso = json_decode($jsonCurso, true);
            $nom = $nombreCurso[0]['nombre'];
            //Limpiar nombre de video
            $nomLimpio = str_replace(['¿', '?', '!', '¡', '"', '\''], '', $videoNombre);
            //Subir el video a la carpeta de cursos
            \Storage::disk('cursos')->put($video_nombre, \File::get($video));
            //Crear carpeta del video
            \Storage::makeDirectory('cursos/' . $nom . '/' . $nomLimpio);
            //Mover video a su carpeta correspondiente
            \Storage::move('cursos/' . $video_nombre, 'cursos/' . $nom . '/' . $nomLimpio . '/' . $video_nombre);
            //Respuesta
            $respuesta = array(
                'status' => 'correcto',
                'mensaje' => 'El video se subio correctamente.',
                'video' => $video_nombre,
                'id' => $id
            );
        }
        //Retornar respuesta
        return response()->json($respuesta);
    }
    //INICIO METODO PARA GUARDAR VIDEO EN EL SERVIDOR

    //INICIO GUARDAR IMAGEN
    public function imagenCurso(Request $request, $nom)
    {
        //Recoger los datos que llegan
        $imagen = $request->file('file0');
        //Validar que sea una imagen
        $validador = \Validator::make($request->all(), [
            'file0' => 'required'
        ]);

        if (!$imagen || $validador->fails()) {
            //error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'Las extenciones que se aceptan son: jpg, png, jpeg y gif.'
            );
        } else {
            //Traer el nombre de la imagen agragando time en formato unix para que no se repitan nombres
            $imagen_nombre = time() . $imagen->getClientOriginalName();
            //guardar imagen en el storage
            \Storage::disk('cursos')->put($imagen_nombre, \File::get($imagen));
            \Storage::move('cursos/' . $imagen_nombre, 'cursos/' . $nom . '/' . $imagen_nombre);
            //guardar video en el storage
            //Respuesta
            $respuesta = array(
                'status' => 'img',
                'mensaje' => 'La imagen se subio correctamente.',
                'imagen' => $imagen_nombre
            );
        }
        //Retornar respuesta
        return response()->json($respuesta);
    }
    //FIN GUARDAR IMAGEN

    //INICIO METODO PARA INSERTAR EL NOMBRE DE LA IMAGEN DEL CURSO EN LA BD
    public function insertarImagen(Request $request, $id)
    {
        //Recoger parametros por post
        $json = $request->input('json', null);
        $parametros_array = json_decode($json, true);

        if (!empty($parametros_array)) {
            //No se actualiza
            unset($parametros_array['id']);
            unset($parametros_array['nombre']);
            unset($parametros_array['carrera']);
            unset($parametros_array['profesor']);
            unset($parametros_array['calificacion']);
            unset($parametros_array['duracion']);
            unset($parametros_array['area']);
            unset($parametros_array['descripcion']);
            //Actualizar campos
            $actualizar = Curso::where('id_curso', $id)->update($parametros_array);

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
    //FIN METODO PARA INSERTAR EL NOMBRE DE LA IMAGEN DEL CURSO EN LA BD

    //INICIO TRAER IMAGEN DE CURSO
    public function traerMultimedia($disk, $curso, $imagen)
    {
        //Recibir datos
        $path = $disk;
        //Comprobar si existe la imagen en el disco
        $isset = \Storage::disk($path)->exists($imagen);
        if ($isset) {
            $archivo = \Storage::disk($path)->get($imagen);
            return new Response($archivo, 200);
        } else {
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'Noexiste la imagen en la base de datos.'
            );
            return response()->json($respuesta);
        }
    }
    //FIN TRAER IMAGEN DE CURSO

    //INICIO CREAR AREA 
    public function crearArea(Request $request)
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
                'nombre' => 'required|unique:cursos',
                'descripcion' => 'required',
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
                //Crear area
                $area = new Area();
                $area->nombre = $parametros_array['nombre'];
                $area->imagen = $parametros_array['imagen'];
                $area->descripcion = $parametros_array['descripcion'];
                //Guardar area
                $area->save();

                //Mensaje de respuesta
                $respuesta = array(
                    'status' => 'correcto',
                    'codigo' => 200,
                    'mensaje' => 'Se creo correctamente el area.',
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
    //FIN CREAR AREA

    //INICIO GUARDAR IMAGEN AREA
    public function imagenAreas(Request $request)
    {
        //Recoger los datos que llegan
        $imagen = $request->file('file0');
        //Validar que sea una imagen
        $validador = \Validator::make($request->all(), [
            'file0' => 'required'
        ]);

        if (!$imagen || $validador->fails()) {
            //error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'Las extenciones que se aceptan son: jpg, png, jpeg y gif.'
            );
        } else {
            //Traer el nombre de la imagen agragando time en formato unix para que no se repitan nombres
            $imagen_nombre = time() . $imagen->getClientOriginalName();
            //guardar imagen en el storage
            \Storage::disk('areas')->put($imagen_nombre, \File::get($imagen));

            //Respuesta
            $respuesta = array(
                'status' => 'correcto',
                'mensaje' => 'La imagen se subio correctamente.',
                'imagen' => $imagen_nombre
            );
        }

        //Retornar respuesta
        return response()->json($respuesta);
    }
    //FIN GUARDAR IMAGEN AREA

    //INICIO TRAER IMAGEN
    public function traerImagen($nombre, $disk)
    {
        //Comprobar si existe la imagen en el disco
        $isset = \Storage::disk($disk)->exists($nombre);
        if ($isset) {
            //Regresar el archivo
            $archivo = \Storage::disk($disk)->get($nombre);
            return  Response($archivo, 200);
        } else {
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'No existe la imagen en la base de datos.'
            );
            return response()->json($respuesta);
        }
    }
    //FIN TRAER IMAGEN

    //INICIO CREAR CARRERA 
    public function crearCarrera(Request $request)
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
                'nombre' => 'required|unique:cursos',
                'descripcion' => 'required',
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
                //Crear carrera
                $carrera = new Carrera();
                $carrera->nombre = $parametros_array['nombre'];
                $carrera->imagen = $parametros_array['imagen'];
                $carrera->descripcion = $parametros_array['descripcion'];
                $carrera->area_id = $parametros_array['area_id'];
                //Guardar carrera
                $carrera->save();

                //Mensaje de respuesta
                $respuesta = array(
                    'status' => 'correcto',
                    'codigo' => 200,
                    'mensaje' => 'Se creo correctamente el carrera.',
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
    //FIN CREAR CARRERA

    //INICIO GUARDAR IMAGEN CARRERAS
    public function imagenCarreras(Request $request)
    {
        //Recoger los datos que llegan
        $imagen = $request->file('file0');
        //Validar que sea una imagen
        $validador = \Validator::make($request->all(), [
            'file0' => 'required'
        ]);

        if (!$imagen || $validador->fails()) {
            //error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'Las extenciones que se aceptan son: jpg, png, jpeg y gif.'
            );
        } else {
            //Traer el nombre de la imagen agragando time en formato unix para que no se repitan nombres
            $imagen_nombre = time() . $imagen->getClientOriginalName();
            //guardar imagen en el storage
            \Storage::disk('carreras')->put($imagen_nombre, \File::get($imagen));

            //Respuesta
            $respuesta = array(
                'status' => 'correcto',
                'mensaje' => 'La imagen se subio correctamente.',
                'imagen' => $imagen_nombre
            );
        }

        //Retornar respuesta
        return response()->json($respuesta);
    }
    //FIN GUARDAR IMAGEN CARRERAS

    //INICIO TRAER CARRERAS
    public function traerCarreras(Request $request)
    {
        $respuesta = Carrera::all();
        return response()->json($respuesta);
    }
    //FIN TRAER CARRERAS

    //INICIO TRAER AREAS
    public function traerAreas(Request $request)
    {
        //Modelo área
        $respuesta = Area::all();
        //Devolver respuesta
        return response()->json($respuesta);
    }
    //FIN TRAER AREAS

    //INICIO TRAER TODAS LAS CARRERAS DE UNA SOLA AREA
    public function carrerasArea($id)
    {
        $respuesta = Area::find($id)->carreras;
        return response()->json($respuesta);
    }
    //FIN TRAER TODAS LAS CARRERAS DE UNA SOLA AREA

    //INICIO TRAER TODOS LOS CURSOS DE UNA CARRERA
    public function cursosCarrera($id)
    {
        //Traer los cursos con el id de la carrera
        $respuesta = \DB::table('join_cursos_carrera')
            ->join('cursos', 'join_cursos_carrera.curso_id', '=', 'cursos.id_curso')
            ->join('usuarios', 'cursos.profesor_id', '=', 'usuarios.id')
            ->select('join_cursos_carrera.carrera_id', 'join_cursos_carrera.curso_id', 'join_cursos_carrera.nombre_curso', 'join_cursos_carrera.nombre_carrera', 'cursos.imagen', 'cursos.descripcion', 'cursos.profesor_id', 'usuarios.nombre')
            ->where('carrera_id', $id)
            ->orderBy('join_cursos_carrera.nombre_curso','asc')
            ->get();
        return response()->json($respuesta);
    }
    //FIN TRAER TODOS LOS CURSOS DE UNA CARRERA

    //INICIO TRAER TODOS LOS CURSOS DE UN AREA
    public function cursosArea($id)
    {
        //Traer los cursos con el id de la area
        $respuesta = Curso::where('area_id', $id)->get();
        return response()->json($respuesta);
    }
    //FIN TRAER TODOS LOS CURSOS DE UN AREA

    //INICIO METODO PARA GUARDAR TEMA
    public function guardarTema(Request $request)
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
                'curso_id' => 'required',
                'numero' => 'required',
                'nombre' => 'required'
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
                //Crear tema
                $tema = new Tema();
                $tema->nombre = $parametros_array['nombre'];
                $tema->curso_id = $parametros_array['curso_id'];
                $tema->numero = $parametros_array['numero'];
                //Guardar tema
                $tema->save();

                //Mensaje de respuesta
                $respuesta = array(
                    'status' => 'correcto',
                    'codigo' => 200,
                    'mensaje' => 'Se creo correctamente el tema.',
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
    //FIN METODO PARA GUARDAR TEMA

    //INICIO METODO PARA TRAER TODOS LOS TEMAS DE UN CURSO
    public function traerTemas($id)
    {
        //Traer los temas
        $respuesta = Tema::where('curso_id', $id)->orderBy('numero', 'ASC')->get();
        return response()->json($respuesta);
    }
    //FIN METODO PARA TRAER TODOS LOS TEMAS DE UN CURSO

    //INICIO METODO PARA TRAER UN TEMA CON SU ID
    public function traerUnTemas($id)
    {
        //Traer los temas
        $respuesta = Tema::where('id', $id)->get();
        return response()->json($respuesta);
    }
    //FIN METODO PARA TRAER UN TEMA CON SU ID


    //INICIO METODO PARA TRAER TODOS LOS VIDEOS DE UN TEMA
    public function videosTemas($id)
    {
        //Traer los videos con el id del tema
        $respuesta = Video::where('tema_id', $id)->orderBy('numero', 'ASC')->get();
        return response()->json($respuesta);
    }
    //FIN METODO PARA TRAER TODOS LOS VIDEOS DE UN TEMA

    //INICIO METODO PARA GUARDAR INFORMACION DE VIDEO
    public function guardarVideoTema(Request $request)
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
                'tema_id' => 'required',
                'numero' => 'required',
                'titulo' => 'required'
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
                //Crear video
                $video = new Video();
                $video->titulo = $parametros_array['titulo'];
                $video->tema_id = $parametros_array['tema_id'];
                $video->numero = $parametros_array['numero'];
                $video->video = $parametros_array['video'];
                //Guardar video
                $video->save();

                //Mensaje de respuesta
                $respuesta = array(
                    'status' => 'correcto',
                    'codigo' => 200,
                    'mensaje' => 'Se creo correctamente el video.',
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
    //FIN METODO PARA GUARDAR INFORMACION DE VIDEO

    //INICIO METODO PARA TRAER UN VIDEO CON SU ID
    public function traerVideo($id)
    {
        //Traer info de video con el id
        $video = Video::where('id', $id)->get();
        return response()->json($video);
    }
    //FIN METODO PARA TRAER UN VIDEO CON SU ID

    //INICIO METODO PARA TRAER UN VIDEO CON SU NOMBRE 
    public function traerVideoNom($titulo, $temaId)
    {
        //Traer info de video con el id del tema y titulo del video
        $video = Video::where('titulo', $titulo)
            ->where('tema_id', $temaId)
            ->get();
        return response()->json($video);
    }
    //FIN METODO PARA TRAER UN VIDEO CON SU NOMBRE 

    //INICIO METODO PARA AGREGAR UN CURSO A UNA CARRERA
    public function agregarCursoCarrea(Request $request)
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
                'carrera_id' => 'required',
                'curso_id' => 'required',
                'nombre_curso' => 'required',
                'nombre_carrera' => 'required'
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
                //Crear modelo 
                $cursoCarrera = new JoinCursoCarrera();
                $cursoCarrera->carrera_id = $parametros_array['carrera_id'];
                $cursoCarrera->curso_id = $parametros_array['curso_id'];
                $cursoCarrera->nombre_curso = $parametros_array['nombre_curso'];
                $cursoCarrera->nombre_carrera = $parametros_array['nombre_carrera'];
                //Guardar curso-Carrera
                $cursoCarrera->save();

                //Mensaje de respuesta
                $respuesta = array(
                    'status' => 'correcto',
                    'codigo' => 200,
                    'mensaje' => 'Se agrego correctamente.',
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
    //FIN METODO PARA AGREGAR UN CURSO A UNA CARRERA

    //INICIO METODO PARA ELIMINAR UN CURSO DE UNA CARRERA
    public function eliminarCursoCarrera($id)
    {
        //Conseguir OBJETO del curso con el $id
        $curso = JoinCursoCarrera::find($id);
        //Comprobar si el id esta en la base de datos
        if ($curso) {
            //Eliminar comentario
            $curso->delete();
            //mensaje respuesta
            $respuesta = array(
                'status' => 'correcto',
                'mensaje' => 'El curso fue borrado de la carrera.'
            );
        } else {
            //mensaje error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'El curso no se encontro en la base de datos.'
            );
        }
        //Retornar respuesta
        return response()->json($respuesta);
    }
    //FIN METODO PARA ELIMINAR UN CURSO DE UNA CARRERA

    //INICIO METODO PARA GUARDAR TEORIA A UN VIDEO
    public function guardarTeoria(Request $request)
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
                'titulo' => 'required',
                'informacion' => 'required',
                'video_id' => 'required'
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
                //Guardar saltos de linea en mysql
                $saltosTex = str_replace('/\<br(\s*)?\/?\>/i', '\n',$parametros_array['informacion']);
                //Crear teoria
                $teoria = new Teoria();
                $teoria->video_id = $parametros_array['video_id'];
                $teoria->titulo = $parametros_array['titulo'];
                $teoria->informacion = $saltosTex;
                $teoria->archivo = $parametros_array['archivo'];
                //Verificar que no este vacio el campo de archivo
                if ($parametros_array['archivo']=='') {
                    $teoria->archivo = '0000000000Sin archivo';
                }
                //Guardar teoria
                $teoria->save();

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
    //FIN METODO PARA GUARDAR TEORIA A UN VIDEO

    //INICIO METODO PARA GUARDAR ARCHIVOS DE TEORIA EN EL SERVIDOR
    public function guardarArchivos(Request $request, $nombreCurso, $nombreVideo, $idTeoria, $campoTabla, $modelo)
    {
        //Recoger los datos que llegan
        $archivo = $request->file('file0');
        //Validar que sea una archivo
        $validador = \Validator::make($request->all(), [
            'file0' => 'required '
        ]);

        if (!$archivo || $validador->fails()) {
            //error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'LHubo un error con el archivo quie intento subir.'
            );
        } else {
            //Traer el nombre de la archivo agragando time en formato unix para que no se repitan nombres
            $archivo_nombre = time() . $archivo->getClientOriginalName();
            //Limpiar nombre de video
            $nomLimpio = str_replace(['¿', '?', '!', '¡', '"', '\''], '', $nombreVideo);
            //Subir el archivo a la carpeta de cursos
            \Storage::disk('cursos')->put($archivo_nombre, \File::get($archivo));
            //Mover archivo a su carpeta correspondiente
            \Storage::move('cursos/' . $archivo_nombre, 'cursos/' . $nombreCurso . '/' . $nomLimpio . '/' . $archivo_nombre);
            //Comprobar si es modelo de teoria o video
            if ($modelo == 'Teoria') {
                //Actualizar campos en BD
                Teoria::where('id', $idTeoria)->update([$campoTabla => $archivo_nombre]);
            } elseif ($modelo == 'Video') {
                //Actualizar campos en BD
                Video::where('id', $idTeoria)->update([$campoTabla => $archivo_nombre]);
            }


            //Respuesta
            $respuesta = array(
                'status' => 'correcto',
                'mensaje' => 'El archivo se subio correctamente.',
                'archivo' => $archivo_nombre
            );
        }
        //Retornar respuesta
        return response()->json($respuesta);
    }
    //FIN METODO PARA GUARDAR ARCHIVOS DE TEORIA EN EL SERVIDOR

    //INICIO METODO PARA TRAER TODA LA TEORIA DE UN VIDEO
    public function teoriaVideo($id)
    {
        $teoria = Teoria::where('video_id', $id)->get();
        return response()->json($teoria);
    }
    //FIN METODO PARA TRAER TODA LA TEORIA DE UN VIDEO

    //INICIO METODO PARA TRAER UNA TEORIA CON SU ID
    public function unaTeoria($id)
    {
        $teoria = Teoria::where('id', $id)->get();
        return $teoria;
    }
    //FIN METODO PARA TRAER UNA TEORIA CON SU ID

    //INICIO METODO PARA TRAER UNA TEORIA CON SU NOMBRE
    public function traerUnTeoriaNombre($videoId, $tituloTeoria)
    {
        //Traer el tema
        $respuesta = Teoria::where('titulo', $tituloTeoria)
            ->where('video_id', $videoId)
            ->get();
        return response()->json($respuesta);
    }
    //FIN METODO PARA TRAER UNA TEORIA CON SU NOMBRE

    //INICIO METODO PARA ACTUALIZAR LA IMAGEN DE UNA CARRERA
    public function actualizaImagenCurso(Request $request, $nombreCurso, $nombreImagen, $idCurso)
    {
        //Recoger los datos que llegan
        $imagen = $request->file('file0');
        //Validar que sea una imagen
        $validador = \Validator::make($request->all(), [
            'file0' => 'required'
        ]);

        if (!$imagen || $validador->fails()) {
            //error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'Las extenciones que se aceptan son: jpg, png, jpeg y gif.'
            );
        } else {
            //Traer el nombre de la imagen agragando time en formato unix para que no se repitan nombres
            $imagen_nombre = time() . $imagen->getClientOriginalName();
            //guardar imagen en el storage
            \Storage::disk('cursos')->put($imagen_nombre, \File::get($imagen));
            //Eliminar imagen guardada anteriormente
            \Storage::disk('cursos')->delete($nombreCurso . '/' . $nombreImagen);
            //Mover imagen a carpeta
            \Storage::move('cursos/' . $imagen_nombre, 'cursos/' . $nombreCurso . '/' . $imagen_nombre);
            //Actualizar campos en BD
            Curso::where('id_curso', $idCurso)->update(['imagen' => $imagen_nombre]);

            //Respuesta
            $respuesta = array(
                'status' => 'correcto',
                'mensaje' => 'La imagen se subio correctamente.',
                'imagen' => $imagen_nombre
            );
        }

        //Retornar respuesta
        return response()->json($respuesta);
    }
    //FIN METODO PARA ACTUALIZAR LA IMAGEN DE UNA CARRERA

    //INICIO METODO PARA ACTUALIZAR LOS DATOS DE UN CURSO
    public function actualizarCurso(Request $request, $id)
    {
        //Recibir datos por POST
        $json = $request->input('json', null);
        $parametros_array = json_decode($json, true);
        //Traer información del curso de la BD
        $curso = Curso::where('id_curso', $id)->get();

        if (!empty($parametros_array)) {

            //Actualizar nombre de carpeta si cambio el nombre del curso
            if ($curso[0]['nombre'] != $parametros_array['nombre']) {
                \Storage::move('cursos/' . $curso[0]['nombre'], 'cursos/' . $parametros_array['nombre']);
            }
            //No se actualiza
            unset($parametros_array['id_curso']);
            unset($parametros_array['profesor']);
            unset($parametros_array['calificacion']);
            unset($parametros_array['imagen']);
            unset($parametros_array['area']);

            //Actualizar campos en tablas
            Curso::where('id_curso', $id)->update($parametros_array);
            JoinCursoCarrera::where('curso_id', $id)->update(['nombre_curso' => $parametros_array['nombre']]);

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
    //FIN METODO PARA ACTUALIZAR LOS DATOS DE UN CURSO

    //INICIO METODO PARA ACTUALIZAR TEMA
    public function actualizarTema(Request $request, $id)
    {
        //Recibir datos por POST
        $json = $request->input('json', null);
        $parametros_array = json_decode($json, true);

        if (!empty($parametros_array)) {
            //No se actualiza
            unset($parametros_array['id']);
            unset($parametros_array['curso_id']);
            //Actualizar campos
            Tema::where('id', $id)->update($parametros_array);
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
    //INICIO METODO PARA ACTUALIZAR TEMA

    //INICIO METODO PARA ACTUALIZAR UN VIDEO
    public function actualizarVideo(Request $request, $id, $nomCurso)
    {
        //Recibir datos por POST
        $json = $request->input('json', null);
        $parametros_array = json_decode($json, true);
        //Traer info de video de BD
        $video = Video::where('id', $id)->get();
        //Limpiar titulo de caracter ?
        $nomLimpio = str_replace(['¿', '?', '!', '¡', '"', '\''], '', $parametros_array['titulo']);
        $nomLimpioAnterior = str_replace(['¿', '?', '!', '¡', '"', '\''], '', $video[0]['titulo']);

        if (!empty($parametros_array)) {
            //Actualizar nombre de carpeta si cambio el nombre del curso
            if ($video[0]['titulo'] != $parametros_array['titulo']) {
                \Storage::move('cursos/' . $nomCurso . '/' . $nomLimpioAnterior, 'cursos/' . $nomCurso . '/' . $nomLimpio);
            }
            //No se actualiza
            unset($parametros_array['id']);
            unset($parametros_array['tema_id']);
            unset($parametros_array['video']);
            //Actualizar campos
            Video::where('id', $id)->update($parametros_array);
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
    //INICIO METODO PARA ACTUALIZAR UN VIDEO

    //INICIO METODO PARA ACTUALIZAR LA TEORIA
    public function actualizarTeoria(Request $request, $id)
    {
        //Recibir datos por POST
        $json = $request->input('json', null);
        $parametros_array = json_decode($json, true);

        if (!empty($parametros_array)) {
            //No se actualiza
            unset($parametros_array['id']);
            unset($parametros_array['video_id']);
            unset($parametros_array['archivo']);
            $saltosTex = str_replace('/\<br(\s*)?\/?\>/i', '\n',$parametros_array['informacion']);
            $parametros_array['informacion']= $saltosTex;
            //Actualizar campos
            Teoria::where('id', $id)->update($parametros_array);
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
    //INICIO METODO PARA ACTUALIZAR LA TEORIA

    //INICIO METODO PARA ACTUALIZAR EL ARCHIVO DE UN VIDEO
    public function actualizarArchivoVideo(Request $request, $nomCurso, $tituloVideo, $videoAnterior, $idVideo, $bd)
    {
        //Recoger los datos que llegan
        $video = $request->file('file0');
        //Validar que sea una video
        $validador = \Validator::make($request->all(), [
            'file0' => 'required '
        ]);

        if (!$video || $validador->fails()) {
            //error
            $respuesta = array(
                'status' => 'error',
                'mensaje' => 'LHubo un error con el archivo quie intento subir.'
            );
        } else {
            //Traer el nombre de la video agragando time en formato unix para que no se repitan nombres
            $archivo_nombre = time() . $video->getClientOriginalName();
            //Subir el video a la carpeta de cursos
            \Storage::disk('cursos')->put($archivo_nombre, \File::get($video));
            //Limpiar nombre de video
            $nomLimpio = str_replace(['¿', '?', '!', '¡', '"', '\''], '', $tituloVideo);
            //Mover video a su carpeta correspondiente
            \Storage::move('cursos/' . $archivo_nombre, 'cursos/' . $nomCurso . '/' . $nomLimpio . '/' . $archivo_nombre);
            //Eliminar video guardada anteriormente
            \Storage::disk('cursos')->delete($nomCurso . '/' . $nomLimpio . '/' . $videoAnterior);
            //Actualizar base de datos seleccionado para videos y teoria
            if ($bd == 1) {
                Video::where('id', $idVideo)->update(['video' => $archivo_nombre]);
            } else {
                Teoria::where('id', $idVideo)->update(['archivo' => $archivo_nombre]);
            }

            //Respuesta
            $respuesta = array(
                'status' => 'correcto',
                'mensaje' => 'El video se subio correctamente.',
                'video' => $archivo_nombre
            );
        }
        //Retornar respuesta
        return response()->json($respuesta);
    }
    //INICIO METODO PARA ACTUALIZAR EL ARCHIVO DE UN VIDEO

    //INICIO METODO PARA TRAER TODOS LOS CURSOS A LOS QUE UN USUARIO ESTA INSCRITO
     public function cursosUsuario($idUsuario)
    {
        $cursos = JoinCursoUsuario::where('id_usuario', $idUsuario)->get();
        return response()->json($cursos);
    }
    //FIN METODO PARA TRAER TODOS LOS CURSOS A LOS QUE UN USUARIO ESTA INSCRITO
}
