<?php

namespace App\Http\Controllers;
use App\Models\Libros;
use App\Models\Alumno;
use App\Models\Pedido;
use App\Models\AlumnoCurso;
use App\Models\CursoLibros;
use Illuminate\Http\Request;
use Validator;
use \stdClass;


class AlumnoController extends Controller
{
    public function __construct()
    {
        $this->Libros = new Libros();
        $this->Alumno = new Alumno();
        $this->Pedido = new Pedido();
        $this->AlumnoCurso = new AlumnoCurso();
        $this->CursoLibros = new CursoLibros();
    }
    /**
     * Busqueda de alumnos segun el idsCursos, despues formatea el rut con su ultimo digito
     * 
     * @author Victor curilao
     */
    public function searchAlumnoCurso(Request $request){
        $idsCurso = $request->idCursos;
        $alumnos = $this->AlumnoCurso->getAlumnosCurso($idsCurso)->values()->all();
     
        if(count($idsCurso) === 0){
          $alumnos = $this->AlumnoCurso->getAlumnosCursoAll()->values()->all();
        }
        $addUltimoDigito = collect($alumnos)->map(function($alumno){
            $alumno->numeroDocumento = $alumno->numeroDocumento.'-'.$this->calculaDV($alumno->numeroDocumento);
            return $alumno;
        });

        return response()->json(['alumnos'=>$alumnos]);
    }
    /**
     * Busqueda del alumno segun el rut entrega todos los datos personales del alumno
     * 
     * @author victor curilao
     */
    public function searchAlumnoRut($rut){
        $alumno = $this->Alumno->getAlumnoDetalleByRut($rut);

        return response()
            ->json([
                'alumno'=>$alumno
            ]);
    }
    /**
     * Método que muestra todos los alumnos de los cursos y con sus detalles
     * agrega ademas el ultimo digito
     * 
     * @author Victor Curilao 
     * */
    public function getAlumnoAll(){
        $alumnos = $this->Alumno->getAlumnoDetalle();

        $addUltimoDigito = $alumnos->map(function($alumno){
            $alumno->numeroDocumento = $alumno->numeroDocumento.'-'.$this->calculaDV($alumno->numeroDocumento);
            return $alumno;
        });
        return response()
        ->json([
            'ok'=>true,
            'alumnos'=>$alumnos
        ]);

    }
    /**
     * Calcula el digito verificador de un RUT.
     * Fuente: http://www.dcc.uchile.cl/~mortega/microcodigos/validarrut/php.php
     * @author Luis Dujovne
     * @param int $r  Un RUT sin DV
     * @return char(1) el digito verificador del RUT
     */
    public function calculaDV($r)
    {
        $s = 1;
        for ($m = 0; $r != 0; $r /= 10) {
            $s = ($s + $r % 10 * (9 - $m++ % 6)) % 11;
        }

        return chr($s ? $s + 47 : 75);
    }
}
