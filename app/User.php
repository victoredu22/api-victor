<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

//Añadimos la clase JWTSubject
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = 'tblAlumno';
    protected $primaryKey = 'idAlumno';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
    * Método que busca al usuario segun el rut.
    *
    * @param $rut
    * @return usuario
    * @author Victor Curilao
    **/
    public function getInfoUsuarioById($rut){
        $getUser = DB::table('tblAlumno')
                    ->where('numeroDocumento', $rut)
                    ->get()
                    ->first();
  
        return  $getUser;
    }

    public function createUser($request){

        $insert = new User;
        $insert->numeroDocumento = $request->numeroDocumento;
        $insert->email = $request->email;
        $insert->password = $request->password;
        $insert->save();

        return $insert;
    }   
    /*
           Añadiremos estos dos métodos
       */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
