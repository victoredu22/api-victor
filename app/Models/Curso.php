<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Curso extends Model
{
  protected $table = "tblCurso";
  protected $primaryKey = "idCurso";

  /**
   * 
   *  Busca las propiedades del curso segun su id busqueda
   * 
   * @return get
   */
  public function getCursoById($idCurso){
    $curso = new Curso;
    $curso = curso::find($idCurso);
    return $curso;
  }
}