<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tests extends Model
{
    protected $table = 'tests';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['nombre','fecha', 'categoria','puntos','idCurso','idUnidad','descripcion','estado','revisado'];
    protected $guarded = [];

    public static function doTest($idTest){
        $currentUser = \Auth::user();
        $res = Responses::where('idUser','=',$currentUser->id)->where('idTest','=',$idTest)->get();
        return sizeof($res)>0;
    }
}
