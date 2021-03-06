<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Alumno extends Model
{
    protected $table = "tblAlumno";
    protected $primaryKey = "idAlumno";

    /**
     * Metodo que trae todos los alumnos de la tabla alumnos
     * 
     * @return $get
     */
    public function getAlumnos()
    {
        $get = DB::table('tblAlumno')
                    ->get();

        return $get;
    }
    /**
     * Metodo que busca datos del alumno segun el idAlumno
     * 
     * @return $get
     */
    public function getAlumnosById($idAlumno)
    {
        $Alumno =  DB::table('tblAlumno')
                    ->join('tblDetalleAlumno', 'tblAlumno.idAlumno', 'tblDetalleAlumno.idAlumno')
                    ->select('tblAlumno.idAlumno', 'tblAlumno.numeroDocumento', 'tblAlumno.email', 'tblDetalleAlumno.nombre', 'tblDetalleAlumno.apellido')
                    ->where('tblAlumno.idAlumno', $idAlumno)
                    ->get()
                    ->first();
        return $Alumno;
    }
    /**
     * Metodo que trae detalles de todos los alumnos del sistema junto al detalleALumno
     *
     * @return get
     */
    public function getAlumnoDetalle()
    {
        $get = DB::table('tblAlumno')
                    ->join('tblDetalleAlumno', 'tblAlumno.idAlumno', 'tblDetalleAlumno.idAlumno')
                    ->select('tblAlumno.idAlumno', 'tblAlumno.numeroDocumento', 'tblAlumno.email', 'tblDetalleAlumno.nombre', 'tblDetalleAlumno.apellido')
                    ->get();
        return $get;
    }

    public function getAlumnoByRut($numeroDocumento)
    {
        $get = DB::table('tblAlumno')
                ->where('numeroDocumento', $numeroDocumento)
                ->get()
                ->first();

        return $get;
    }
}
