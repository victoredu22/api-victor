<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LibroStock;

use DB;

class Libros extends Model
{
    protected $table = "tblLibro";
    protected $primaryKey = "idLibro";
    /**
     * Metodo con el metodo find que me trae la info del libro segun el id
     * 
     * @return get
     */
    public function getLibroId($idLibro){
        $libros = new Libros;
        $libros = libros::find($idLibro);
        return $libros;
    }
    /**
     * Metodo que trae todos los libros que se encuentran en el sistema
     * 
     * @return get
     */
    public function getLibros(){
        $get = DB::table('tblLibro') 
            ->join('tblLibroStock','tblLibro.idLibro','=','tblLibroStock.idLibro')
            ->select('tblLibro.idLibro','tblLibro.nombreLibro','tblLibro.autor','tblLibro.detalle','tblLibro.estado','tblLibroStock.cantidad','tblLibro.destino')
            ->get();
        return $get;
    }
    /**
     * Metodo que trae informacion del libro segun el id
     * 
     * @return get
     */
    public function findLibroId($request){
        $get = DB::table('tblLibro')
                ->join('tblLibroStock','tblLibro.idLibro','=','tblLibroStock.idLibro')
                ->select('tblLibro.idLibro','tblLibro.nombreLibro','tblLibro.autor','tblLibro.detalle','tblLibro.estado','tblLibroStock.cantidad')
                ->where('tblLibro.idLibro',$request->idLibro)
                ->first();
        return $get;
    }

    
    /**
     * Metodo que actualiza los libros segun el idLibro
     * Actializa en dos tablas en tblLibros y tblLibroStock
     * 
     * @return update
     */
    public function updateLibro($request){
        $libro = Libros::find($request->idLibro);
        $libro->nombreLibro = $request->nombreLibro;
        $libro->autor = $request->autor;
        $libro->destino = $request->destino;
        $libro->save();

        $libroStock = LibroStock::find($request->idLibro);
        $libroStock->cantidad = $request->cantidad;
        $libroStock->save();

        return $libro;
    }

    /**
     * Metodo que crea libros y ademas crea el stock de este en su tabla
     * 
     * @return insert
     */
    public function createLibro($request){
        $libro = new Libros;
        $libro->nombreLibro = $request->nombreLibro;
        $libro->autor = $request->autor;
        $libro->destino = $request->destino;
        $libro->save();
        
      
        $libroStock = new LibroStock;
        $libroStock->idLibro = $libro->idLibro;
        $libroStock->cantidad = $request->cantidad;
        $libroStock->save();

        return $libro;
    }
}
