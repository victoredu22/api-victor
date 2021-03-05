<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Libros;
use App\Models\Alumno;
use App\Models\Pedido;
use App\Models\CursoLibros;
use Validator;
use \stdClass;

class LibrosController extends Controller
{
    public function __construct()
    {
        $this->Libros = new Libros();
        $this->Alumno = new Alumno();
        $this->Pedido = new Pedido();
        $this->CursoLibros = new CursoLibros();
    }
    /**
     * Método que muestra todos los libros que se encuentran en el sistema
     *
     * @param ninguno
     * */
    public function getLibrosAll()
    {
        $libros = $this->Libros->getLibros();

        return response()
            ->json([
                'ok'=>true,
                'libros'=>$libros
            ]);
    }
    /**
     * Método que arrienda un libro segun el id de este y el rut del alumno
     *
     * @return responseJson libros
     * @author Victor Curilao
     * */
    public function pedidoLibro(Request $request)
    {   
        $reglas = array(
            "rutAlumno" => "required",
            "fechaEntrega" => "required",
        );
        $msgValidacion = array(
            "required" => "es un campo obligatorio."
        );
        $validador = Validator::make($request->all(), $reglas, $msgValidacion);
        if ($validador->fails()) {
            return response()
            ->json([
                'ok'=>false,
                "msg" => "validacionError",
                "errores"=>$validador->errors()
            ]);
        }


        $busquedaAlumno = $this->Alumno->getAlumnoByRut($request->rutAlumno);
        $pedidoLibro = $this->Pedido->getIdPedido($request->idLibro, $busquedaAlumno->idAlumno);

        if ($pedidoLibro) {
            return response()
                ->json([
                    'ok'=>false,
                    'msg'=>"libroEncontrado"
                ]);
        }

        $createPedido = $this->Pedido->createPedido($request->idLibro, $busquedaAlumno->idAlumno,$request->fechaEntrega);
        $disminuicionLibro = $this->Libros->disminucionLibro($request);
        
        return response()
            ->json([
                'ok'=>true,
                'msg'=>"libroArrendado"
            ]);
    }
    public function updateLibro(Request $request)
    {
        $reglas = array(
            "nombreLibro" => "required",
            "cantidad" => "required",
            "autor" => "required",
            "destino" => "required"
        );
        $msgValidacion = array(
            "required" => "es un campo obligatorio."
        );
        $validador = Validator::make($request->all(), $reglas, $msgValidacion);
        if ($validador->fails()) {
            return response()
            ->json([
                'ok'=>false,
                "msg" => "validacionError",
                "errores"=>$validador->errors()
            ]);
        }
        
        $updateLibro = $this->Libros->updateLibro($request);
        $getLibro = $this->Libros->findLibroId($request);
        
        if ($updateLibro) {
            return response()
                ->json([
                    'ok'=>true,
                    "msg"=>"libroActualizado",
                    "libro"=>$getLibro
                ]);
        }
    }
    public function createLibro(Request $request)
    {
        $reglas = array(
            "nombreLibro" => "required",
            "cantidad" => "required",
            "autor" => "required",
            "idCurso"=>"required",
            "destino" => "required"
        );
        $msgValidacion = array(
            "required" => "es un campo obligatorio."
        );
        $validador = Validator::make($request->all(), $reglas, $msgValidacion);
        if ($validador->fails()) {
            return response()
            ->json([
                'ok'=>false,
                "msg" => "validacionError",
                "errores"=>$validador->errors()
            ]);
        }
        
        $libro = $this->Libros->createLibro($request);
        $createCursos = $this->CursoLibros->insertCursoLibro($request, $libro);

        if ($createCursos) {
            return response()
                ->json([
                    'ok'=>true,
                    "msg"=>"libroCreado",
                    "libro"=>$libro,
                    'cursoLibro'=>$createCursos
                ]);
        }
    }

    public function librosSinStock()
    {
        $libros = $this->Libros->getLibros();
        $librosSinStock = $libros->filter(function ($elem, $key) {
            return $elem->cantidad == 0;
        })->values()->all();

        return response()
                ->json([
                    'ok'=>true,
                    "librosSinStock"=>$librosSinStock
                ]);
    }

    public function librosPedidos()
    {
        $pedidos = $this->Pedido->getPedido();
        $pedidosActivos = $pedidos->filter(function ($elem, $key) {
            return $elem->activo == 1;
        });

        $pedidosLibros = $pedidosActivos->groupBy('idLibro')->values()->all();
        $conjuntoLibros = collect($pedidosLibros)->map(function ($elem, $key) {
            $groupPedidos = $elem->map(function ($elem, $key) {
                return $elem->idLibro;
            });
            $libro = new stdClass;
            $libro->idLibro = collect($groupPedidos)->first();
            $libro->cantidad = count($groupPedidos);
            return $libro;
        });
        $addNombreLibro = $conjuntoLibros->map(function($libro){
            $dataLibro = $this->Libros->findLibroId($libro);
            return $libro->nombreLibro = $dataLibro->nombreLibro;
        });
 
        return response()
                ->json([
                    'ok'=>true,
                    "conjuntoLibros"=>$conjuntoLibros
                ]);
    }

    public function infoLibroId($idLibro)
    {   
        $request = new stdClass;
        $request->idLibro = $idLibro;
      
        $libro = $this->Libros->findLibroId($request);
    
        return response()
            ->json([
                'ok'=>true,
                'libro'=>$libro
            ]);
    }
    
}
