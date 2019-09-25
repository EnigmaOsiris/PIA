<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'ratings';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['idDeliverable','idUser', 'score','state','typeDeliverable'];
    protected $guarded = [];
}
