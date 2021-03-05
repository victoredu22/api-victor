<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Pedido extends Model
{
    protected $table = "tblPedido";
    protected $primaryKey = "idPedido";
    public function getPedido(){
        $get = DB::table('tblPedido')    
        ->get();
        return $get;
    }
    public function getPedidosActivos(){
        $get = DB::table('tblPedido')
                ->join('tblLibro','tblLibro.idLibro','tblPedido.idLibro')
                ->join('tblAlumno','tblAlumno.idAlumno','tblPedido.idAlumno')
                ->join('tblDetalleAlumno','tblDetalleAlumno.idAlumno','tblAlumno.idAlumno')
                ->where('tblPedido.activo',1)
                ->select('tblPedido.idPedido','tblPedido.idLibro','tblPedido.idAlumno','tblpedido.fechaEntrega',
                        'tblAlumno.idAlumno','tblDetalleAlumno.nombre','tblDetalleAlumno.apellido','tblLibro.nombreLibro')
                ->get();

        return $get;
    }
    public function getUltimosPedidos(){

        $get = DB::table('tblPedido')
                ->join('tblLibro','tblLibro.idLibro','tblPedido.idLibro')
                ->join('tblAlumno','tblAlumno.idAlumno','tblPedido.idAlumno')
                ->join('tblDetalleAlumno','tblDetalleAlumno.idAlumno','tblAlumno.idAlumno')
                ->where('tblPedido.activo',1)
                ->select('tblPedido.idPedido','tblPedido.idLibro','tblPedido.idAlumno','tblpedido.fechaEntrega','tblPedido.activo','tblPedido.estado',
                        'tblAlumno.idAlumno','tblDetalleAlumno.nombre','tblDetalleAlumno.apellido','tblLibro.nombreLibro','tblPedido.created_at')
                ->orderBy('created_at', 'DESC')
                ->get();

        return $get;
    }

    public function getIdPedido($idLibro,$idAlumno){
        $get = DB::table('tblPedido')
                ->where('idLibro',$idLibro)
                ->where('idAlumno',$idAlumno)
                ->where('activo',1)
                ->get()
                ->first();
        return $get;
    }

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
                ->whereBetween('fechaPedido', [$fechaInicio, $fechaTermino])
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

    public function getPedidoAlumno($idAlumno){
        $get = DB::table('tblPedido')
                ->where('idAlumno',$idAlumno)
                ->where('estado',1)
                ->where('activo',1)
                ->get();
        return $get;
    }


    public function createPedidoFecha($request){
        $libro = new Pedido;
        $libro->idLibro = $request->idLibro;
        $libro->idAlumno = $request->idAlumno;
        $libro->fechaEntrega = $request->fechaEntrega;
        $libro->activo = 1;
        $libro->save();
        return $libro;
    }
    public function updateEstado($request){

        $pedido = new Pedido;
        $pedido = pedido::find($request->idPedido);
        $pedido->estado = $request->estado;
        $pedido->save();

        return $pedido;
    }
}
