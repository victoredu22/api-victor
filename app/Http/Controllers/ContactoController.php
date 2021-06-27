<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contacto;
use Validator;


class ContactoController extends Controller
{   
    public function __construct(){
        $this->Contacto = new Contacto();
    }

    /**
     * Metodo que inserta  un nuevo contacto en la bd, puede venir desde cumbre o tour huellas
     * 
     * @author Victor Curilao
     */
    public function insertContacto(Request $request){
        $reglas = array(
            'email'=>"required",
            'mensaje'=>"required",
            'tipoContacto'=>"required",
        );
        $msg = ['required'=>"Es un campo obligatorio"];

        $validador = Validator::make($request->all(), $reglas, $msg);

        if($validador->fails()){
            return response()
            ->json([
                'ok'=>false,
                "msg"=>"errorValidacion",
                "errores"=>$validador->errors()
            ]);
        }

        $insert = $this->Contacto->insertContacto($request);
        return response()->json([
            'ok'=>true,
            'msg'=>'Se ha ingresado el contacto con exito.'
        ]);
    
    }
}
