<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Pedido extends Model
{
    protected $table = "tblPedido";
    protected $primaryKey = "idPedido";

    /**
     * Metodo que busca todos los pedidos junto con el stock del libro
     * 
     * @return $get
     */
    public function getPedido(){
        $get = DB::table('tblPedido')  
                ->join('tblLibroStock','tblPedido.idLibro','=','tblLibroStock.idLibro')  
                ->get();
        return $get;
    }
    /**
     * Metodo que busca todos los pedidos activos del sistema
     * 
     * @return $get
     */
    public function getPedidosActivos(){
        $get = DB::table('tblPedido')
                ->join('tblLibro','tblLibro.idLibro','tblPedido.idLibro')
                ->join('tblAlumno','tblAlumno.idAlumno','tblPedido.idAlumno')
                ->join('tblDetalleAlumno','tblDetalleAlumno.idAlumno','tblAlumno.idAlumno')
                ->where('tblPedido.activo',1)
                ->select('tblPedido.idPedido','tblPedido.idLibro','tblPedido.idAlumno','tblPedido.fechaEntrega',
                        'tblAlumno.idAlumno','tblDetalleAlumno.nombre','tblDetalleAlumno.apellido','tblLibro.nombreLibro')
                ->get();

        return $get;
    }
    /**
     * Consulta que trae los ultimos pedidos de la base datos en base de alumno y libros
     * los ordena segun la fecha de la creacion
     * 
     * @return get
     */
    public function getUltimosPedidos(){

        $get = DB::table('tblPedido')
                ->join('tblLibro','tblLibro.idLibro','tblPedido.idLibro')
                ->join('tblAlumno','tblAlumno.idAlumno','tblPedido.idAlumno')
                ->join('tblDetalleAlumno','tblDetalleAlumno.idAlumno','tblAlumno.idAlumno')
                ->where('tblPedido.activo',1)
                ->select('tblPedido.idPedido','tblPedido.idLibro','tblPedido.idAlumno','tblPedido.fechaEntrega','tblPedido.activo','tblPedido.estado',
                        'tblAlumno.idAlumno','tblDetalleAlumno.nombre','tblDetalleAlumno.apellido','tblLibro.nombreLibro','tblPedido.created_at')
                ->orderBy('created_at', 'DESC')
                ->get();

        return $get;
    }
    /**
     * Consulta que trae los ultimos pedidos de la base datos en base de alumno y libros
     * los ordena segun la fecha de la creacion
     * 
     * @return get
     */
    public function getUltimosPedidosPaginate(){

        $get = DB::table('tblPedido')
                ->join('tblLibro','tblLibro.idLibro','tblPedido.idLibro')
                ->join('tblAlumno','tblAlumno.idAlumno','tblPedido.idAlumno')
                ->join('tblDetalleAlumno','tblDetalleAlumno.idAlumno','tblAlumno.idAlumno')
                ->leftJoin('tblAlumnoCurso','tblAlumnoCurso.idAlumno','tblAlumno.idAlumno')
                ->leftJoin('tblCurso','tblAlumnoCurso.idCurso','tblCurso.idCurso')
                ->where('tblPedido.activo',1)
                ->select('tblPedido.idPedido','tblPedido.idLibro','tblPedido.idAlumno','tblPedido.fechaEntrega','tblPedido.activo','tblPedido.estado',
                        'tblAlumno.idAlumno','tblDetalleAlumno.nombre','tblDetalleAlumno.apellido','tblLibro.nombreLibro','tblPedido.fechaRetiro','tblPedido.estadoRetiro','tblPedido.estadoEntrega','tblCurso.nombreCurso')
                ->orderBy('fechaRetiro', 'DESC');

        return $get;
    }

    public function getUltimosPedidosSearch($buscador){
        
        $get = DB::table('tblPedido')
                ->join('tblLibro','tblLibro.idLibro','tblPedido.idLibro')
                ->join('tblAlumno','tblAlumno.idAlumno','tblPedido.idAlumno')
                ->join('tblDetalleAlumno','tblDetalleAlumno.idAlumno','tblAlumno.idAlumno')
                ->leftJoin('tblAlumnoCurso','tblAlumnoCurso.idAlumno','tblAlumno.idAlumno')
                ->leftJoin('tblCurso','tblAlumnoCurso.idCurso','tblCurso.idCurso')
                
                ->where('tblPedido.activo',1)
                ->where('tblLibro.nombreLibro', 'LIKE', '%'.$buscador.'%')
                ->select('tblPedido.idPedido','tblPedido.idLibro','tblPedido.idAlumno','tblPedido.fechaEntrega','tblPedido.activo','tblPedido.estado',
                        'tblAlumno.idAlumno','tblDetalleAlumno.nombre','tblDetalleAlumno.apellido','tblLibro.nombreLibro','tblPedido.fechaRetiro','tblPedido.estadoRetiro','tblPedido.estadoEntrega','tblCurso.nombreCurso')
                ->orderBy('fechaRetiro', 'DESC');

        return $get;
    }
    /**
     * Metodo que busca el pedido segun el idLibro y el idALumno
     * 
     * @return get
     */
    public function getIdPedido($idLibro,$idAlumno){
        $get = DB::table('tblPedido')
                ->where('idLibro',$idLibro)
                ->where('idAlumno',$idAlumno)
                ->where('activo',1)
                ->get()
                ->first();
        return $get;
    }
    /**
     * Metodo que crea un nuevo pedido segun los paremetros entrantes
     *
     * @return insert
     */
    public function createPedido($idLibro,$idAlumno,$fechaEntrega){
        $libro = new Pedido;
        $libro->idLibro = $idLibro;
        $libro->idAlumno = $idAlumno;
        $libro->fechaEntrega = $fechaEntrega;
        $libro->activo = 1;
        $libro->save();
        return $libro;
    }

    public function getPedidoMes($fechaInicio,$fechaTermino){
        $get = DB::table('tblPedido')
                ->whereBetween('created_at', [$fechaInicio, $fechaTermino])
                ->where('activo',1)
                ->get();

        return $get;
    }
    
    public function getPedidoAño($año){
        $get = DB::table('tblPedido')
                ->where('fechaPedido', 'like', '%' . $año . '%')
                ->where('activo',1)
                ->get();
                
        return $get;

    }
    public function getPedidosMensuales($fecha){
        $get = DB::table('tblPedido')
                ->where('fechaPedido', 'like', '%' . $fecha . '%')
                ->where('activo',1)
                ->get();
                
        return $get;
    }
    /**
     * Metodo que obiene datos del pedido segun el idAlumno
     * 
     * @return get
     */
    public function getPedidoAlumno($idAlumno){
        $get = DB::table('tblPedido')
                ->where('idAlumno',$idAlumno)
                ->where('estado',1)
                ->where('activo',1)
                ->get();
        return $get;
    }

    /**
     * Metodo que crea un nuevo pedido
     * @return insert
     */
    public function createPedidoFecha($request){

        $libro = new Pedido;
        $libro->idLibro = $request->idLibro;
        $libro->idAlumno = $request->idAlumno;
        $libro->fechaEntrega = $request->fechaEntrega;
        $libro->estadoRetiro = $request->estadoRetiro;
        $libro->activo = 1;
        $libro->save();
        return $libro;
    }

    public function getPedidoId($idLibro){
        $pedido = new Pedido;
        $pedido = pedido::find($idLibro);
        return $pedido;
    }
    /**
     * Actualiza el estado del pedido
     * 
     * @return update pedido
     */
    public function updateEstado($request){

        $pedido = new Pedido;
        $pedido = pedido::find($request->idPedido);
        $pedido->estado = $request->estado;
        $pedido->estadoEntrega = $request->estadoEntrega;
        $pedido->save();

        return $pedido;
    }

    public function getPedidoAll($numeroPaginate){
        $get = DB::table('tblPedido')->paginate($numeroPaginate);
        return $get;
    }
}   
