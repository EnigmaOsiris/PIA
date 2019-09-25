<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    protected $table = 'archivos';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['idCurso','idUnidad', 'nombre','descripcion','url','estado'];
    protected $guarded = [];
}
