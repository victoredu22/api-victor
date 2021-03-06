<?php

namespace App\Http\Controllers;

use App\Models\AlumnoCurso;
use App\Models\Curso;
use App\Models\Libros;
use App\Models\LibroStock;
use App\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use \stdClass;

class PedidosController extends Controller
{
    public function __construct()
    {
        $this->Pedido = new Pedido();
        $this->Libro = new Libros();
        $this->LibroStock = new LibroStock();
        $this->AlumnoCurso = new AlumnoCurso();
        $this->Curso = new Curso();
    }
    public function searchLibros(Request $request)
    {

        switch ($request) {
            case $request->buscador != "undefined":
                $getPedido = $this->buscadorPedido($request);
                break;
            case $request->idAlumno != "undefined":
                $getPedido = $this->alumnoPedido($request);
                break;
            case count($request->idCursos) > 0:
                $getPedido = $this->cursoPedido($request);
                break;
            default:
                $getPedido = $this->Pedido->pedidosAll();
                break;
        }

        $getPedidos = $getPedido->paginate(5);

        return response()->json([
            'ok' => true,
            'request'=>$request->all(),
            'pagination' => [
                'total' => $getPedidos->total(),
                'current_page' => $getPedidos->currentPage(),
                'per_page' => $getPedidos->perPage(),
                'last_page' => $getPedidos->lastPage(),
                'from' => $getPedidos->firstItem(),
                'to' => $getPedidos->lastPage(),
            ],
            'getPedidos' => $getPedidos,
        ]);
    }
    /**
     * Busca 
     * 
     * @author victor curilao
     */
    public function searchPedidoCursoLibro(Request $request){

        $idCursos = $request->idCursos;

        $getPedidos = $this->Pedido->getPedidoCursoLibro($idCursos,$request->buscador)->paginate(5);

        return response()->json([
            'ok'=>true,
            'pagination'=>[
                'total'=>$getPedidos->total(),
                'current_page'=>$getPedidos->currentPage(),
                'per_page'=>$getPedidos->perPage(),
                'last_page'=>$getPedidos->lastPage(),
                'from'=>$getPedidos->firstItem(),
                'to'=>$getPedidos->lastPage()
            ],
            'getPedidos'=>$getPedidos
        ]);

    }
    public function searchPedidoAlumno($idAlumno){
    
        $getPedidos = $this->Pedido->getPedidoByAlumno($idAlumno)->paginate(5);
        return response()->json([
            'ok'=>true,
            'pagination'=>[
                'total'=>$getPedidos->total(),
                'current_page'=>$getPedidos->currentPage(),
                'per_page'=>$getPedidos->perPage(),
                'last_page'=>$getPedidos->lastPage(),
                'from'=>$getPedidos->firstItem(),
                'to'=>$getPedidos->lastPage()
            ],
            'getPedidos'=>$getPedidos
        ]);
    }
    public function alumnoPedido($request)
    {
        return $this->Pedido->pedidosAll()
            ->where('tblPedido.idAlumno', $request->idAlumno);
    }
    public function buscadorPedido($request)
    {

        switch ($request) {
            case count($request->idCursos) > 0:
                $seleccion = 'idCursos';
                break;
            case $request->idAlumno != "undefined":
                $seleccion = 'alumnos';
                break;
            default:
                $seleccion = null;
                break;
        }
        

        $buscador = [
            "idCursos" => $this->Pedido->pedidosAll()
                ->whereIn('tblAlumnoCurso.idCurso', $request->idCursos)
                ->where('nombreLibro', 'LIKE', '%' . $request->buscador . '%'),
            "alumnos" => $this->Pedido->pedidosAll($request)
                ->where('nombreLibro', 'LIKE', '%' . $request->buscador . '%')
                ->where('tblPedido.idAlumno', $request->idAlumno),
        ];

        return $seleccion != null
        ? $buscador[$seleccion]
        : $this->Pedido->pedidosAll()
            ->where('nombreLibro', 'LIKE', '%' . $request->buscador . '%');
    }


    /**
     * Metodo que obtiene los pedido segun el IdAlumno
     *
     * @author Victor Curilao
     */
    public function infoPedidoAlumno($idAlumno)
    {
        $pedidoAlumno = $this->Pedido->getPedidoAlumno($idAlumno);
        $totalPedidos = count($pedidoAlumno);

        return response()->json([
            'ok' => true,
            'totalPedido' => $totalPedidos,
            'pedidoAlumno' => $pedidoAlumno,
        ]);
    }
    /**
     * Metodos que muestra los ultimos pedidos que estan activos
     * asociando libros y alumnos
     *
     * @author Victor Curilao
     */
    public function pedidosRecientes()
    {
        $getPedido = $this->Pedido->getUltimosPedidos();

        return response()->json([
            'ok' => true,
            'getPedido' => $getPedido,
        ]);
    }
    /**
     * Metodo que nos trae todos los libros pendientes de acuerdo a la llamada de este
     *
     * @author Victor Curilao
     */
    public function librosPendientes()
    {
        $date = Carbon::now();
        $fechaHoy = $date->format('Y-m-d');
        $getPedido = $this->Pedido->getPedidosActivos();

        $filtroFechas = $getPedido->map(function ($elem, $key) use ($fechaHoy) {
            $fechaEntrega = explode(" ", $elem->fechaEntrega);
            $fechaEntrega = $fechaEntrega[0];
            return (Carbon::parse($fechaEntrega)->lt($fechaHoy) == true) ? $elem : null;
        });
        $alumnosNotNull = $filtroFechas->filter(function ($elem, $key) {
            return $elem != null;
        })->values()->all();

        return response()->json([
            'ok' => true,
            'listadoAlumno' => $alumnosNotNull,
        ]);
    }
    /**
     * Muestra la cantidad de pedidos que se encuentran en el mes de ingreso a la pagina
     *
     * @author Victor Curilao
     */
    public function pedidosMes()
    {
        $date = Carbon::now();

        $fechaInicio = $date->startOfMonth()->format('Y-m-d');
        $fechaTermino = $date->endOfMonth()->format('Y-m-d');
        $getPedidos = $this->Pedido->getPedidoMes($fechaInicio, $fechaTermino);
        return response()->json([
            'ok' => true,
            'getPedidos' => $getPedidos,
        ]);
    }

    public function porcentajePedidos()
    {
        $date = Carbon::now();
        $a??o = $date->year;

        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        $mesesNumero = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');

        $pedidoA??o = $this->Pedido->getPedidoA??o($a??o);
        $sumaPedido = count($pedidoA??o);

        $totalPedidosA??o = collect($mesesNumero)->map(function ($elem, $key) use ($a??o, $sumaPedido) {
            $instanciaMes = $a??o . '-' . $elem;
            $fechaInicio = Carbon::parse($instanciaMes)->startOfMonth()->format('Y-m-d');
            $fechaTermino = Carbon::parse($instanciaMes)->endOfMonth()->format('Y-m-d');

            return count($this->Pedido->getPedidoMes($fechaInicio, $fechaTermino));
        });

        $a??oLibros = collect($meses)->map(function ($elem, $keyMes) use ($totalPedidosA??o) {
            $libro = new stdClass;
            $libro->mes = $elem;
            $libro->porcentaje = $totalPedidosA??o->filter(function ($elemPedido, $keyPedido) use ($keyMes) {
                return $keyMes === $keyPedido;
            })->values()->first();

            return $libro;
        });
        return response()->json([
            'ok' => true,
            'totalPedidosA??o' => $a??oLibros,
        ]);
    }
    /**
     * Metodo que crea pedidos de alumnos y libros que hayan solicitado,
     * parametros idLibro, idAlumno, fechaEntrega
     * Ademas disminuye en la tabla stock de acuardo al pedido que se hizo
     *
     */
    public function createPedido(Request $request)
    {
        $reglas = array(
            "idLibro" => "required",
            "idAlumno" => "required",
            "fechaEntrega" => "required",
        );
        $msgValidacion = array(
            "required" => "es un campo obligatorio.",
        );
        $validador = Validator::make($request->all(), $reglas, $msgValidacion);
        if ($validador->fails()) {
            return response()
                ->json([
                    'ok' => false,
                    "msg" => "validacionError",
                    "errores" => $validador->errors(),
                ]);
        }

        $pedidoLibro = $this->Pedido->getIdPedido($request->idLibro, $request->idAlumno);
        if ($pedidoLibro) {
            return response()
                ->json([
                    'ok' => false,
                    'msg' => "libroEncontrado",
                ]);
        }

        $getLibro = $this->Libro->findLibroId($request);

        if ($getLibro->cantidad == 0) {
            return response()
                ->json([
                    'ok' => false,
                    'msg' => 'libroSinStock',
                ]);
        }

        $disminuicion = $this->LibroStock->disminucionStock($request);
        $createPedido = $this->Pedido->createPedidoFecha($request);
        $getPedido = $this->Pedido->getPedidoId($createPedido->idPedido);
        $getLibro = $this->Libro->getLibroId($createPedido->idLibro);

        $getAlumnoCurso = $this->AlumnoCurso->getAlumnoByIdAlumno($getPedido->idAlumno);
        $curso = $this->Curso->getCursoById($getAlumnoCurso->idCurso);
        return response()
            ->json([
                'ok' => true,
                'pedido' => $getPedido,
                'libro' => $getLibro,
                'curso' => $curso,
            ]);
    }
    /**
     * Metodo que actualiza el estado del pedido segun el idLibro y idALumno
     *
     * @author Victor Curilao
     */
    public function updateEstadoPedido(Request $request)
    {

        $updatePedido = $this->Pedido->updateEstado($request);
        return response()
            ->json([
                'ok' => true,
                'msg' => "estadoActualizado",
                'updatePedido' => $updatePedido,
            ]);
    }
}
