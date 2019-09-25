<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deliverable extends Model
{
    protected $table = 'deliverables';
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ['idDeliverable','idUser', 'type','isCheck','state'];
    protected $guarded = [];


    public static function getStudent($id){
        return User::find($id);
    }

    public static function getScore($idDeliverable,$idUser){
        return Rating::where('idDeliverable','=',$idDeliverable)->where('idUser','=',$idUser)->first();
    }

    public static function getDeliverable($id,$type){
        if($type==0){
            #HomwWorks
        }else{ #Test
         $data=   Tests::find($id);
         //$data=   Tests::find(17);
        }
        return $data;
    }
}
