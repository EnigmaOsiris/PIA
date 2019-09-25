<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subscriptions;
use App\Course;
use App\Temarios;
use App\Unit;
use App\Seccion;
use App\Files;
use App\Tests;
use App\Preguntas;
use App\Discusiones;
use App\ComentariosDisc;
use App\Responses;
use App\Deliverable;
use App\User;

class SubscriptionsController extends Controller
{
    public function suscribe($data){
        $currentUser = \Auth::user();
        $course = Course::find($data);
        if(!self::isSuscribe($course,$currentUser)){
            #Suscribe
            $s = new Subscriptions();
            $s->idUser = $currentUser->id;
            $s->idCourse = $course->id;
            $s->state=0;
            $s->save();
        }
        
       
        $currentDate = date('d/m/y');
        $cursos = self::getCourses();
        $cn = new \App\Http\Controllers\SubscriptionsController();
        return view('pia.std.cursos.mostrarCursos',compact('cursos','currentDate','cn','currentUser'));
    }


    public function showSubscriptions(){
        $currentUser = \Auth::user();
        $courses = Subscriptions::join('users','users.id','=','subscriptions.idUser')
            ->join('courses','courses.id','=','subscriptions.idCourse')
            ->where('idUser','=',$currentUser->id)->get();
        $currentDate = date('d/m/y');
        $cn = new Course();
        return view('pia.std.subscriptions.mostrarCursos',compact('courses','currentUser','currentDate','cn'));      
    }


    public function isSuscribe($c,$u){
        return sizeof(Subscriptions::where('idUser','=',$u->id)->where('idCourse','=',$c->id)->get())>0;
    }


    public function getCourses(){
        $courses = Course::orderBy('id','desc')->paginate(7);
        $currentUser = \Auth::user();
        foreach($courses as $c){
            if(self::isSuscribe($c,$currentUser)){
                $c->state=1;
            }else{
                $c->state=0;
            }
        }
        return $courses;
    }


    public function showContent($id){
        $curso = Course::find($id);
        $curso->estado=1;
        $curso->update();
        $currentDate = date('d/m/y');
        $temario = Temarios::where('idCurso','=',$curso->id)->get();
        $unidades = Unit::where('idCourse','=',$curso->id)->get();
        $students = Subscriptions::join('users','users.id','=','subscriptions.idUser')
            ->join('courses','courses.id','=','subscriptions.idCourse')
            ->where('idCourse','=',$curso->id)->get();
        return view('pia.std.cursos.contentCourse',compact('curso','currentDate','temario','unidades','students'));
    }


    public function showUnit($curso){
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $currentDate = date('d/m/y');
        $cosas = array();
        $secciones = Seccion::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $archivos = Files::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->where('idSection','=',-1)->get();
        $tests = Tests::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->where('idSection','=',-1)->get();
        $discusiones = Discusiones::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->where('idSection','=',-1)->get();
        return view('pia.std.cursos.showUnit',compact('curso','currentDate','unidad','cosas','secciones','archivos','tests','discusiones'));
    }

    public function showSection($curso){
         
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idSection = explode('|',$curso)[2];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);

        $section = Seccion::find($idSection);
        
        $currentDate = date('d/m/y');
        $cosas = array();
        $secciones = Seccion::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $archivos = Files::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->where('idSection','=',$idSection)->get();
        $tests = Tests::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->where('idSection','=',$idSection)->get();
        $discusiones = Discusiones::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->where('idSection','=',$idSection)->get();
        return view('pia.std.cursos.viewSection',compact('curso','currentDate','unidad','cosas','secciones','archivos','tests','discusiones','section'));
    }

    public function showDiscussion($curso){
        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idDiscusion = explode('|',$curso)[2];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $discusion = Discusiones::find($idDiscusion);
        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;
        $comentarios = ComentariosDisc::where('idDiscusion','=',$idDiscusion)->get();
        return view('pia.std.cursos.showDiscussion',compact('curso','currentDate','unidad','discusion','opt','msg','result','comentarios'));

    }

    public function getStudentStatus($id){
        $user =  User::find($id);
        return view('pia.std.cursos.showStudentState',compact('user'));
    }

    public function showContentExam($data){
        $currentDate = date('y-m-d');
        $idCurso = explode('|',$data)[0];
        $idUnidad = explode('|',$data)[1];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $idTest = explode('|',$data)[2];
        $idSection = -1;
        $section = new Seccion();
        $section->id=-1;
        $dat = explode('|',$data);
        if(sizeof($dat)>3){
            $idTest = explode('|',$data)[3];
            $idSection = explode('|',$data)[2];
            $section = Seccion::find($idSection);
        }
        $test = Tests::find($idTest);
        $questions = Preguntas::where('idTest','=',$idTest)->get();
        $status = 0;        
        if('20'.$currentDate > $test->fecha){
            $status = 1;
        }
        return view('pia.std.cursos.showExam',compact('curso','unidad','section','test','status','questions'));
    }
    
    public function answerTest(Request $test,$data){
        
        $scanner = explode('|',$data);

        $idCurso = explode('|',$data)[0];
        $idUnidad = explode('|',$data)[1];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $idTest = explode('|',$data)[2];
        $idSection = -1;
        $section = new Seccion();
        $section->id=-1;
        if(sizeof($scanner)>3){
            $idTest = explode('|',$data)[3];
            $idSection = explode('|',$data)[2];
            $section = Seccion::find($idSection);
        }
        $testSelected = Tests::find($idTest);
        $id=$idTest;

        
        $yn = Preguntas::where('idTest','=',$id)->where('tipo','=',1)->get();
        $oq = Preguntas::where('idTest','=',$id)->where('tipo','=',2)->get();
        $mo = Preguntas::where('idTest','=',$id)->where('tipo','=',3)->get();
        $currentUser = \Auth::user();
        $r='';
        $i=0;
        foreach ($yn as $val) {
            $t = $test->input('yesno'.$i);
            
            $res = new Responses();
            $res->idQuestion = $val->id;
            $res->idCourse = $idCurso;
            $res->idUnit = $idUnidad;
            $res->idSection = $section->id;
            $res->response = $t;
            $res->state=0;
            $res->description = '';
            $res->dateRealization = date('y-m-d');
            $res->idUser = $currentUser->id;
            $res->idTest = $id;
            $res->save();
            $r=$r.'|'.$t;
            $i++;
        }
        $i=0;
        foreach ($oq as $val) {
            $t = $test->input('open'.$i);
            $res = new Responses();
            $res->idQuestion = $val->id;
            $res->idCourse = $idCurso;
            $res->idUnit = $idUnidad;
            $res->idSection = $section->id;
            $res->response = $t;
            $res->state=0;
            $res->description = '';
            $res->dateRealization = date('y-m-d');
            $res->idUser = $currentUser->id;
            $res->idTest = $id;
            $res->save();
            $r=$r.'|'.$t;
            $i++;
        }
        $i=0;
        foreach ($mo as $val) {
            $t = $test->input('options'.$i);
            $res = new Responses();
            $res->idQuestion = $val->id;
            $res->idCourse = $idCurso;
            $res->idUnit = $idUnidad;
            $res->idSection = $section->id;
            $res->response = $t;
            $res->state=0;
            $res->description = '';
            $res->dateRealization = date('y-m-d');
            $res->idUser = $currentUser->id;
            $res->idTest = $id;
            $res->save();
            $r=$r.'|'.$t;
            $i++;
        }
        
        $deli = new Deliverable();
        $deli->idDeliverable = $idTest;
        $deli->idUser = $currentUser->id; #Who
        $deli->type = 1; #Test,Tareas
        $deli->isCheck=0;
        $deli->state=0;
        $deli->idCourse = $idCurso;
        $deli->idUnit = $idUnidad;
        $deli->save();

        if($section->id==-1){
            return 'showUnit/'.$idCurso.'|'.$idUnidad;
            
        }else{
            return 'showSection/'.$idCurso.'|'.$idUnidad.'|'.$section->id;
            
        }
        
    }


    public function doTest($idTest,$idUser){
        $res = Responses::where('idUser','=',$idUser)->where('idTest','=',$idTest)->get();
        return sizeof($res)>0;
    }

}
