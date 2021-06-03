<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

//INICIO RUTAS DE LA API
//RUTAS DE REGISTRO
Route::prefix('registrar')->group(function () {
    Route::post('/alumno', 'usuarioController@registrarAlumno');
    Route::post('/profesor', 'usuarioController@registrarProfesor');
    Route::post('/curso', 'usuarioController@registrarseACurso');   // Usuario se registra a un curso
});
//RUTA DE LOGIN
Route::post('/login', 'usuarioController@login');
//RUTAS DE INFORMACION
Route::prefix('informacion')->group(function () {
    Route::get('/alumno', 'usuarioController@infoAlumno');
    Route::get('/cursos-alumno/{id}', 'usuarioController@traerCursosAlumno');
});
//RUTAS DE PERFIL
Route::prefix('perfil')->group(function () {
    Route::post('/actualizar-informacion', 'usuarioController@informacionAlumno');
    Route::get('/mi-informacion/{id}', 'usuarioController@infoAlumno');
    Route::post('/actualizar-informacion/{id}', 'usuarioController@ajusteInfoAlumno');
});
//RUTAS GENERALES
Route::prefix('general')->group(function () {
    Route::get('/traer-permisos-usuario/{id}', 'generalController@traerPermisos');
    Route::post('/contacto', 'generalController@contacto');
});
// RUTAS DE BLOG
Route::prefix('blog')->group(function () {
    Route::post('/actualizar-blog/{id}', 'blogController@actualizarBlog');
    Route::get('/eliminar-blog/{id}', 'blogController@eliminarBlog');
    Route::post('/blog', 'blogController@guardarBlog');
    Route::get('/traer-un-blog/{nombre}', 'blogController@traerBlog');
    Route::post('/guardar-imagen-blog/{nombreBlog}', 'blogController@imagenBlog');
    Route::post('/actualizar-contenido-blog/{id}', 'blogController@actualizarContenidoBlog');
    Route::get('/eliminar-contenido-blog/{id}', 'blogController@eliminarContenidoBlog');
    Route::get('/traer-un-blog/{nombre}/{idUsuario}', 'blogController@traerBlog');
    Route::post('/guardar-imagen-blog/{nombreBlog}/{idBlog}', 'blogController@imagenBlog');
    Route::post('/contenido-blog', 'blogController@guardarContenidoBlog');
    Route::get('/traer-contenido-blog/{id}', 'blogController@traerContenidoBlog');  
});
//RUTAS DE CURSO
Route::prefix('curso')->group(function () {
    Route::post('/actualizar-imagen-carrera/{curso}/{imagen}/{id}', 'cursosController@actualizaImagenCurso');
    Route::post('/actualizar-curso/{id}', 'cursosController@actualizarCurso');
    Route::post('/actualizar-tema/{id}', 'cursosController@actualizarTema');
    Route::post('/actualizar-teoria/{id}', 'cursosController@actualizarTeoria');
    Route::post('/actualizar-video/{id}/{nomCurso}', 'cursosController@actualizarVideo');
    Route::post('/actualizar-video-archivo/{nomCurso}/{tituloVideo}/{videoAnterior}/{idVideo}/{bd}', 'cursosController@actualizarArchivoVideo');
    Route::post('/agregar-curso-carrera', 'cursosController@agregarCursoCarrea');
    Route::get('/contenido-curso', 'cursosController@contenidoCurso');
    Route::post('/crear-area', 'cursosController@crearArea');
    Route::post('/crear-carrera', 'cursosController@crearCarrera');
    Route::post('/crear-curso', 'cursosController@crearCurso');
    Route::get('/eliminar-curso-carrera/{id}', 'cursosController@eliminarCursoCarrera');
    Route::get('/encontrar-carrera/{nombre}', 'cursosController@carrera');
    Route::post('/guardar-video/{id}/{videoNombre}', 'cursosController@guardarVideo');
    Route::post('/guardar-curso-profesor', 'cursosController@guardarCursoProfesor');
    Route::post('/guardar-curso-profesor', 'cursosController@guardarCursoProfesor');
    Route::post('/guardar-tema', 'cursosController@guardarTema');
    Route::post('/guardar-teoria', 'cursosController@guardarTeoria');
    Route::post('/guardar-archivo/{nombreCurso}/{nombreVideo}/{idTeoria}/{campoTabla}/{modelo}', 'cursosController@guardarArchivos');
    Route::post('/guardar-tema-video', 'cursosController@guardarVideoTema');
    Route::post('/insertar-imagen/{id}', 'cursosController@insertarImagen');
    Route::post('/imagen-areas', 'cursosController@imagenAreas');
    Route::post('/imagen-carreras', 'cursosController@imagenCarreras');
    Route::get('/todos-cursos', 'cursosController@traerCursos');
    Route::get('/traer-areas', 'cursosController@traerAreas');
    Route::get('/traer-imagen-areas/{nombre}/{disk}', 'cursosController@traerImagen');
    Route::get('/traer-imagen/{nombre}/{disk}', 'cursosController@traerImagen');
    Route::get('/traer-carreras-area/{id}', 'cursosController@carrerasArea');
    Route::get('/traer-cursos-area/{id}', 'cursosController@cursosArea');
    Route::get('/traer-un-curso/{id}', 'cursosController@unCursoId');
    Route::get('/traer-cursos-profesor/{id}', 'cursosController@cursosProfesor');
    Route::get('/traer-cursos-carrera/{id}', 'cursosController@cursosCarrera');
    Route::get('/traer-carreras', 'cursosController@traerCarreras');
    Route::get('/traer-videos-tema/{id}', 'cursosController@videosTemas');
    Route::get('/traer-videos-tema-nombre/{titulo}/{temaId}', 'cursosController@traerVideoNom');
    Route::get('/traer-un-tema/{id}', 'cursosController@traerUnTemas');
    Route::get('/traer-temas/{id}', 'cursosController@traerTemas');
    Route::get('/traer-teoria-nombre/{videoId}/{tituloTeoria}', 'cursosController@traerUnTeoriaNombre');
    Route::get('/traer-video/{id}', 'cursosController@traerVideo');
    Route::get('/traer-una-teoria/{id}', 'cursosController@unaTeoria');
    Route::get('/traer-cursos-usuario/{id}', 'cursosController@cursosUsuario');
    Route::get('/traer-teoria-video/{id}', 'cursosController@teoriaVideo');
    Route::get('/traer-profesores-area/{id}', 'cursosController@profesoresArea');
    Route::get('/traer-uncurso-profesor/{idprof}/{nombre}', 'cursosController@unCursoProf');
    Route::get('/traer-multimedia/{carpeta}/{archivo}/{disk}', function ($carpeta,$archivo,$disk) {
        return Storage::response("$disk/$carpeta/$archivo");
    });
    Route::get('/traer-multimedia-video/{nomCurso}/{tema}/{video}', function ($nomCurso,$tema,$video) {
        //Limpiar nombre de video
        $nomLimpio = str_replace(['¿', '?', '!', '¡', '"', '\''], '', $tema);
        return Storage::response("cursos/$nomCurso/$nomLimpio/$video");
    });
    
    Route::get('/descargar-multimedia-video/{nomCurso}/{tema}/{video}', function ($nomCurso,$tema,$video) {
        //Limpiar nombre de video
        $nomLimpio = str_replace(['¿', '?', '!', '¡', '"', '\''], '', $tema);
        return Storage::response("cursos/$nomCurso/$nomLimpio/$video");
    });
    
});
//RUTAS DE COMENTARIOS
Route::prefix('comentarios')->group(function () {
    Route::post('/guardar', 'comentariosController@guardarComentario');
    Route::get('/traer', 'comentariosController@traerComentarios');
    Route::get('/borrar/{id}', 'comentariosController@borrarComentario');
});
//FIN RUTAS DE LA API