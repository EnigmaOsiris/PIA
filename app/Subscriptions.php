<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    protected $table= 'subscriptions';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['idUser', 'idCourse', 'state'];
    protected $guarded = [];
}
