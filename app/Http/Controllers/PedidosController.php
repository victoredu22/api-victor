<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Libros;
use App\Models\LibroStock;
use App\Models\AlumnoCurso;
use App\Models\Curso;

use Illuminate\Http\Request;

use Carbon\Carbon;
use \stdClass;
use Validator;

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
    public function searchPedidoAlumnoLibro(Request $request){
       
  
        $getPedidos = $this->Pedido->getPedidoAlumnoLibro($request)->paginate(5);
  

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
    /**
     * 
     */
    public function searchCursoByIds(Request $request){
       
      
      

        $idCursos = $request->idCursos;
    
        $getPedidos = $this->Pedido->getCursosByIds($idCursos)->paginate(5);

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

    /**
     * Metodo que recibe como parametro el campo buscador, para luego buscarlo en el modelo pedido
     * entrega el listado total de los resultados y ademas variables de paginacion
     * 
     * @author Victor curilao
     */
    public function searchPedido(Request $request){
        $request = $request->all();
        $buscador = $request['buscador'];

        $getPedidos = $this->Pedido->getUltimosPedidosSearch($buscador)->paginate(5);
        
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
            ///cambiar a getpedidos!!!!!!!!!!!!!!
            'getPedidos'=>$getPedidos
        ]);
    }


    public function getPedidos(Request $request){

/* 
        $getPedidos = $this->Pedido->getPedidoAll(2);
 */
        $getPedidos = $this->Pedido->getUltimosPedidosPaginate()->paginate(5);
   
        return response()->json([
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
            'ok'=>true,
            'totalPedido'=>$totalPedidos,
            'pedidoAlumno'=>$pedidoAlumno
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
            'ok'=>true,
            'getPedido'=>$getPedido
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
            return (Carbon::parse($fechaEntrega)->lt($fechaHoy) == true)  ? $elem : null;
        });
        $alumnosNotNull =  $filtroFechas->filter(function ($elem, $key) {
            return $elem != null;
        })->values()->all();
        
        return response()->json([
            'ok'=>true,
            'listadoAlumno'=>$alumnosNotNull
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
            'ok'=>true,
            'getPedidos'=>$getPedidos
        ]);
    }
    
    public function porcentajePedidos()
    {
        $date = Carbon::now();
        $año = $date->year;
        
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        $mesesNumero = array('01','02','03','04','05','06','07','08','09','10','11','12');

        $pedidoAño = $this->Pedido->getPedidoAño($año);
        $sumaPedido = count($pedidoAño);
      
        $totalPedidosAño = collect($mesesNumero)->map(function ($elem, $key) use ($año, $sumaPedido) {
            $instanciaMes = $año.'-'.$elem;
            $fechaInicio = Carbon::parse($instanciaMes)->startOfMonth()->format('Y-m-d');
            $fechaTermino = Carbon::parse($instanciaMes)->endOfMonth()->format('Y-m-d');

            return count($this->Pedido->getPedidoMes($fechaInicio, $fechaTermino));
        });

        $añoLibros = collect($meses)->map(function ($elem, $keyMes) use ($totalPedidosAño) {
            $libro = new stdClass;
            $libro->mes = $elem;
            $libro->porcentaje = $totalPedidosAño->filter(function ($elemPedido, $keyPedido) use ($keyMes) {
                return $keyMes === $keyPedido;
            })->values()->first();

            return $libro;
        });
        return response()->json([
            'ok'=>true,
            'totalPedidosAño'=>$añoLibros
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
            "fechaEntrega" => "required"
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

        
        $pedidoLibro = $this->Pedido->getIdPedido($request->idLibro, $request->idAlumno);
        if ($pedidoLibro) {
            return response()
                ->json([
                    'ok'=>false,
                    'msg'=>"libroEncontrado"
                ]);
        }



        $getLibro = $this->Libro->findLibroId($request);

        if ($getLibro->cantidad == 0) {
            return response()
            ->json([
                'ok'=>false,
                'msg'=>'libroSinStock'
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
                'ok'=>true,
                'pedido'=>$getPedido,
                'libro'=>$getLibro,
                'curso'=>$curso
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
                'ok'=>true,
                'msg'=>"estadoActualizado",
                'updatePedido'=>$updatePedido
            ]);
    }
}
