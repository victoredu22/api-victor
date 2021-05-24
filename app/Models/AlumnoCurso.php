<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class AlumnoCurso extends Model
{
  protected $table = "tblAlumnoCurso";
  protected $primaryKey = "idAlumnoxcurso";

  /**
   * 
   *  Busca las propiedades del curso segun su id busqueda
   * 
   * @return get
   */
  public function getAlumnoId($idAlumnoCurso){
    $alumnoCurso = new AlumnoCurso;
    $alumnoCurso = alumnoCurso::find($idAlumnoCurso);
    return $alumnoCurso;
  }
  public function getAlumnoByIdAlumno($idAlumno){

    $alumno = DB::table('tblAlumnoCurso')
              ->where('idAlumno',$idAlumno)
              ->get()
              ->first();
       
    return $alumno;
  }

  public function getAlumnosCurso($idsCurso){

    $get = DB::table('tblAlumnoCurso')
        ->whereIn('tblAlumnoCurso.idCurso',$idsCurso)
        ->leftJoin('tblAlumno','tblAlumnoCurso.idAlumno','=','tblAlumno.idAlumno')
        ->leftJoin('tblDetalleAlumno','tblAlumnoCurso.idAlumno','=','tblDetalleAlumno.idAlumno')
        ->select('tblAlumnoCurso.idAlumno','tblAlumnoCurso.idAlumnoxcurso','tblAlumno.numeroDocumento','tblDetalleAlumno.nombre','tblDetalleAlumno.apellido')
        ->get()->unique('idAlumno');
      return $get; 
  }
  public function getAlumnosCursoAll(){

    $get = DB::table('tblAlumnoCurso')
        ->leftJoin('tblAlumno','tblAlumnoCurso.idAlumno','=','tblAlumno.idAlumno')
        ->leftJoin('tblDetalleAlumno','tblAlumnoCurso.idAlumno','=','tblDetalleAlumno.idAlumno')
        ->select('tblAlumnoCurso.idAlumno','tblAlumnoCurso.idAlumnoxcurso','tblAlumno.numeroDocumento','tblDetalleAlumno.nombre','tblDetalleAlumno.apellido')
        ->get()->unique('idAlumno');
      return $get; 
  }
}