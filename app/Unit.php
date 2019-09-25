<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'units';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['idCourse', 'name', 'initDate','endDate'];
    protected $guarded = [];
}
