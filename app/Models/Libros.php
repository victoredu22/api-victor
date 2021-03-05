<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Libros extends Model
{
    protected $table = "tblLibro";
    protected $primaryKey = "idLibro";

    /**
     * Metodo que trae todos los libros que se encuentran en el sistema
     * @return get
     */
    public function getLibros(){
        $get = DB::table('tblLibro') 
            ->get();
        return $get;
    }
     
    public function findLibroId($request){
        $libro = Libros::find($request->idLibro);
        return $libro;
    }

    public function disminucionLibro($request){
        $libro = Libros::find($request->idLibro);       
        $libro->cantidad --;
        $libro->save();
        return $libro;
    }

    public function updateLibro($request){
        $libro = Libros::find($request->idLibro);
        $libro->nombreLibro = $request->nombreLibro;
        $libro->cantidad = $request->cantidad;
        $libro->autor = $request->autor;
        $libro->destino = $request->destino;
        $libro->save();
        return $libro;
    }
    public function createLibro($request){
        $libro = new Libros;
        $libro->nombreLibro = $request->nombreLibro;
        $libro->autor = $request->autor;
        $libro->cantidad = $request->cantidad;
        $libro->destino = $request->destino;
        $libro->save();
        return $libro;
    }
}
