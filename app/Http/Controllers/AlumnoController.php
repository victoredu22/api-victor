<?php

namespace App\Http\Controllers;
use App\Models\Libros;
use App\Models\Alumno;
use App\Models\Pedido;
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
        $this->CursoLibros = new CursoLibros();
    }
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
