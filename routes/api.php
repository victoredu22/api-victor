<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::post('login','LoginController@login');

Route::post('register','LoginController@register');
Route::get('imagenLogin','LoginController@imagenLogin');

Route::post('ingresoContacto','ContactoController@insertContacto');



Route::group(['middleware' => ['jwt.verify','cors'] ], function(){
    Route::get('loginLogeado','LoginController@loginLogeado');

    Route::get('librosPendientes','PedidosController@librosPendientes');
    Route::get('librosSinStock','LibrosController@librosSinStock');
    Route::get('infoLibroId/{idLibro}','LibrosController@infoLibroId');
 

    Route::get('getUsuario','LoginController@getUsuario');
    Route::get('libros','LibrosController@getLibrosAll');
    Route::get('alumnos','AlumnoController@getAlumnoAll');

    Route::get('pedidosRecientes','PedidosController@pedidosRecientes');

    
    Route::get('porcentajePedidos','PedidosController@porcentajePedidos');
    Route::get('librosPedidos','LibrosController@librosPedidos');

    Route::get('pedidosMes','PedidosController@pedidosMes');
    Route::get('infoPedidoAlumno/{idAlumno}','PedidosController@infoPedidoAlumno');

    Route::post('arriendo-libros','LibrosController@pedidoLibro');
    Route::post('update-libro','LibrosController@updateLibro');
    Route::post('create-libro','LibrosController@createLibro');
    Route::post('create-pedido','PedidosController@createPedido');

    Route::post('updateEstadoLibro','PedidosController@updateEstadoPedido');


    Route::post('searchAlumnoRut/{rut}','AlumnoController@searchAlumnoRut');
   
    

    Route::post('searchAlumnoCurso','AlumnoController@searchAlumnoCurso');
  


    

    Route::post('searchLibros','PedidosController@searchLibros');


    Route::get('cursoAll','CursoController@getCurso');
});