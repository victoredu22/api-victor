<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CursoLibros extends Model
{
    protected $table = "tblCursoLibro";
    protected $primaryKey = "idCursoLibro";

    public function insertCursoLibro($request, $libro){
    
        $cursoLibros = new CursoLibros;
        $cursoLibros->idCurso = $request->idCurso;
        $cursoLibros->idLibro = $libro->idLibro;
        $cursoLibros->activo = 1;
        $cursoLibros->save();

        return $cursoLibros;
    }




}
