<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SectionLinks extends Model
{
   public $table = 'section_links';
   public $fillable = ['name','description'];
}
