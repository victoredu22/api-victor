<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Contacto extends Model
{
    protected $table = "tblContacto";
    protected $primaryKey = "idContacto";

    public function insertContacto($request){
        $contacto = new Contacto;
        $contacto->email = $request->email;
        $contacto->telefono = $request->telefono;
        $contacto->mensaje = $request->mensaje;
        $contacto->tipoContacto = $request->tipoContacto;
        $contacto->save();

        return $contacto;
    }
}
