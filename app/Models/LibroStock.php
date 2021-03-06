<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class LibroStock extends Model
{   
    protected $table = "tblLibroStock";
    protected $primaryKey = "idStock";

    /**
     * Metodo que disminuye en uno el stock del libro que fue pedido segun su id
     * 
     * @return update
     */
    public function disminucionStock($request){
        $libroStock = LibroStock::find($request->idLibro);
        $libroStock->cantidad --;
        $libroStock->save();
            
        return $libroStock;
    }
}
