<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Responses extends Model
{
    protected $table = 'responses';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['idQuestion','idCourse', 'idUnit','response','state','description','dateRealization'];
    protected $guarded = [];
}
