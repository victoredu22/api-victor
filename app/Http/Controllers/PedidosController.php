<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Libros;
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
    }
    /*  */
    public function infoPedidoAlumno($idAlumno){
     
        $pedidoAlumno = $this->Pedido->getPedidoAlumno($idAlumno);
        $totalPedidos = count($pedidoAlumno);

        return response()->json([
            'ok'=>true,
            'totalPedido'=>$totalPedidos,
            'pedidoAlumno'=>$pedidoAlumnoF
        ]);
    }
    public function pedidosRecientes()
    {
        $getPedido = $this->Pedido->getUltimosPedidos();

        return response()->json([
            'ok'=>true,
            'getPedido'=>$getPedido
        ]);
    }
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
      
        $totalPedidosAño = collect($mesesNumero)->map(function ($elem, $key) use ($año,$sumaPedido) {
            $instanciaMes = $año.'-'.$elem;
            $fechaInicio = Carbon::parse($instanciaMes)->startOfMonth()->format('Y-m-d');
            $fechaTermino = Carbon::parse($instanciaMes)->endOfMonth()->format('Y-m-d');

            return count($this->Pedido->getPedidoMes($fechaInicio, $fechaTermino) );
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

    public function createPedido(Request $request){

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

        if($getLibro->cantidad == 0){
            return response()
            ->json([
                'ok'=>false,
                'msg'=>'libroSinStock'
            ]);
        }

        $disminuicion = $this->Libro->disminucionLibro($request);
        $createPedido = $this->Pedido->createPedidoFecha($request);
        return response()
            ->json([
                'ok'=>true,
                'pedido'=>$createPedido
            ]); 
    }

    public function updateEstadoPedido(Request $request){
        
        $updatePedido = $this->Pedido->updateEstado($request);
        return response()
            ->json([
                'ok'=>true,
                'msg'=>"estadoActualizado",
                'updatePedido'=>$updatePedido
            ]); 
    }
}
