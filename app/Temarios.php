<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Temarios extends Model
{
    protected $table = 'temarios';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['idCurso', 'nombre', 'url'];
    protected $guarded = [];
}
