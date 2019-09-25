<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
class Course extends Model
{
    public $table = 'courses';
    public $fillable = ['idUsuario','name','credits','subject','level','estado'];

    public function getUser($id){
        $user = User::find($id);
        return $user;
    }
}
