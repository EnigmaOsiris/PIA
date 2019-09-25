<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComentariosDisc extends Model
{
    protected $table = 'comentarios_disc';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['idUsuario','idDiscusion', 'comentario','fecha','estado'];
    protected $guarded = [];

    public static function getUser($id){
        $user = \App\User::find($id);
        return $user;
    }
}
