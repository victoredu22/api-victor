<?php

namespace App\Http\Controllers;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function __construct()
    {
        $this->Curso = new Curso();
    }
    /**
     * Obtiene todos los cursos de la bd
     * 
     * @author victor curilao
     */
    public function getCurso(){
        $getCurso = $this->Curso->getCursoAll();
        return response()->json([
            'ok'=>true,
            'getCurso'=>$getCurso
        ]);
    }
}
