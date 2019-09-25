<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Link;
use App\SectionLinks;
class PublicController extends Controller
{
    public function goAbout(){
        return  view('public.about');
    }

    public function goLibrary(){
        $links = Link::orderBy('created_at','desc')->paginate(10);
        $sections = SectionLinks::orderBy('name','desc')->getQuery()->get();
        $currentDate = date('Y-m-d');
       // return view('pia.admin.library.index',compact('links','sections','currentDate'));
        return  view('public.library')->with('sections',$sections)
        ->with('links',$links);
        ;
    }
}
