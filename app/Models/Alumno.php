<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Alumno extends Model
{
    protected $table = "tblAlumno";
    protected $primaryKey = "idAlumno";
    public function getAlumnos(){
        $alumnos = DB::table('tblAlumno')
                    ->get();

        return $alumnos;
    }
    public function getAlumnosById($idAlumno){
        $Alumno =  DB::table('tblAlumno')
                    ->join('tblDetalleAlumno','tblAlumno.idAlumno','tblDetalleAlumno.idAlumno')
                    ->select('tblAlumno.idAlumno','tblAlumno.numeroDocumento','tblAlumno.email','tblDetalleAlumno.nombre','tblDetalleAlumno.apellido')
                    ->where('tblAlumno.idAlumno',$idAlumno)
                    ->get()
                    ->first();
        return $Alumno;
    }
    public function getAlumnoDetalle(){
        $alumnos = DB::table('tblAlumno')
                    ->join('tblDetalleAlumno','tblAlumno.idAlumno','tblDetalleAlumno.idAlumno')
                    ->select('tblAlumno.idAlumno','tblAlumno.numeroDocumento','tblAlumno.email','tblDetalleAlumno.nombre','tblDetalleAlumno.apellido')
                    ->get();
        return $alumnos;
    }

    public function getAlumnoByRut($numeroDocumento){
        $get = DB::table('tblAlumno')
                ->where('numeroDocumento',$numeroDocumento)
                ->get()
                ->first();

        return $get;
    }
}
