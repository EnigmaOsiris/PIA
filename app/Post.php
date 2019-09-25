<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    public $fillable = ['idUser','title','summary','introduction','content','conclusion','img','url'];
}
