<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use User;
class Mensajes extends Model
{
    protected $table = 'mensajes';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['idUsuarioR','idUsuarioE', 'titulo','mensaje','estado','estado'];
    protected $guarded = [];

    public static function getUser($id){
        $user = \App\User::find($id);
        return $user;
    }
}
