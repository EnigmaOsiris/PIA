<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Preguntas extends Model
{
    protected $table = 'preguntas';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['tipo','pregunta', 'descripcion','opciones','respuestas','estado','idTest','valor'];
    protected $guarded = [];
}
