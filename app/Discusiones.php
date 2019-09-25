<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discusiones extends Model
{
    protected $table = 'discusiones';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['titulo','descripcion', 'fecha','estado','idCurso','idUnidad'];
    protected $guarded = [];


    public static function getUser($id){
        $user = \App\User::find($id);
        return $user;
    }
}
