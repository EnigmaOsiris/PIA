<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'secciones';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['idCourse','idUnidad', 'nombre','color','descripcion'];
    protected $guarded = [];
}
