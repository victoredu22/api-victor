<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CursoLibros extends Model
{
    protected $table = "tblCursoLibro";
    protected $primaryKey = "idCursoLibro";
    /**
     * Metodo que asocia el libro con el curso
     * 
     * @author Victor Curilao
     */
    public function insertCursoLibro($request, $libro){
    
        $cursoLibros = new CursoLibros;
        $cursoLibros->idCurso = $request->idCurso;
        $cursoLibros->idLibro = $libro->idLibro;
        $cursoLibros->activo = 1;
        $cursoLibros->save();

        return $cursoLibros;
    }




}
