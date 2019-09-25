<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use User;
class Link extends Model
{
    public $table = 'links';
    public $fillable = ['idSection','idUser','url','umgUrl','name','description','code'];

    public static function getUser($id){
        $user = \App\User::find($id);
        return $user;
    }
}
