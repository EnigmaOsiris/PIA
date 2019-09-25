<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Post;
use App\Course;
use App\Mensajes;
class HomePanelController extends Controller
{
    

    public function goAdminState(){
        $currentUser = \Auth::user();        
        $docentes = User::orderBy('names','desc')->take(5)->get();
        $eventos = Post::orderBy('created_at','desc')->take(8)->get();
        if(!isset($currentUser)){
            return redirect('/');
       }
        return view('pia.admin.index',compact('docentes','eventos'))->with('user',$currentUser);
    }


    public function verCursos(){
        $currentUser = \Auth::user();        
        $docentes = User::orderBy('names','desc')->take(5)->get();
        $eventos = Post::orderBy('created_at','desc')->take(8)->get();
        $cursos = Course::all();
        return view('pia.admin.academia.cursos',compact('docentes','eventos','cursos'))->with('user',$currentUser);
    }


    public function verDocentes(){
        $currentUser = \Auth::user();        
        //$docentes = User::orderBy('names','desc')->take(5)->get();
        $eventos = Post::orderBy('created_at','desc')->take(8)->get();
        $cursos = Course::all();
        $docentes = User::where('userType','=',3)->get();
        return view('pia.admin.academia.docentes',compact('docentes','eventos','cursos'))->with('user',$currentUser);
    }


    public function verAlumnos(){
        $currentUser = \Auth::user();        
        $docentes = User::orderBy('names','desc')->take(5)->get();
        $eventos = Post::orderBy('created_at','desc')->take(8)->get();
        $cursos = Course::all();

        $alumnos = User::where('userType','=',4)->get();

        if(!isset($currentUser)){
             return redirect('/');
        }

        return view('pia.admin.academia.alumnos',compact('alumnos','docentes','eventos','cursos'))->with('user',$currentUser);
    }


    public function nuevoMensaje(){
        $currentUser = \Auth::user();        
        $usuarios = User::orderBy('names','desc')->get();
        return view('pia.admin.mensajes.nuevo',compact('usuarios'))->with('user',$currentUser);
    }


    public function guardarMensaje(Request $r){
        $currentUser = \Auth::user();        
        
        $m = new Mensajes();
        $m->idUsuarioR = $r->get('usuarior');
        $m->idUsuarioE = $currentUser->id;
        $m->titulo = $r->get('titulo');
        $m->mensaje = $r->get('mensaje');
        $m->estado=0;
        $m->fecha = date('y-m-d');
        $m->save();

        $usuarios = User::orderBy('names','desc')->get();
        $mensajes = Mensajes::where('idUsuarioE','=',$currentUser->id)->orderBy('id','desc')->get();
        $msg='Enviado correctamente.';
        return view('pia.admin.mensajes.enviados',compact('mensajes','msg'))->with('user',$currentUser);
    }

    public function bandeja(){
        $currentUser = \Auth::user();        
        $mensajes = Mensajes::where('idUsuarioR','=',$currentUser->id)->orderBy('id','desc')->get();
        return view('pia.admin.mensajes.bandeja',compact('mensajes'))->with('user',$currentUser);
    }

    public function enviados(){
        $currentUser = \Auth::user();        
        $mensajes = Mensajes::where('idUsuarioE','=',$currentUser->id)->orderBy('id','desc')->get();
        return view('pia.admin.mensajes.enviados',compact('mensajes'))->with('user',$currentUser);
    }


    public function mostrarMensaje($tipo){
        
        $idMensaje= explode('|',$tipo)[0];
        $opt= explode('|',$tipo)[1];        
        $currentUser = \Auth::user();        



        $mensaje = Mensajes::find($idMensaje);

        if($opt=='1'){
            $mensaje->estado=1;
            $mensaje->update();
        }

        return view('pia.admin.mensajes.mostrarMensaje',compact('mensaje','opt'))->with('user',$currentUser);
    }

    public function goTeachState(){
        $currentUser = \Auth::user();        
        $docentes = User::orderBy('names','desc')->take(5)->get();
        $eventos = Post::orderBy('created_at','desc')->take(8)->get();
        if(!isset($currentUser)){
            return redirect('/');
       }
        return view('pia.teach.index',compact('docentes','eventos'))->with('user',$currentUser);
    }

    public function goStdState(){
        $currentUser = \Auth::user();        
        $docentes = User::orderBy('names','desc')->take(5)->get();
        $eventos = Post::orderBy('created_at','desc')->take(8)->get();
        if(!isset($currentUser)){
            return redirect('/');
       }
        return view('pia.std.index',compact('docentes','eventos'))->with('user',$currentUser);
    }

    
}
