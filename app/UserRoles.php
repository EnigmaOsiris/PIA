<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
    protected $table= 'user_roles';
    public $fillable = ['name','value'];
}
