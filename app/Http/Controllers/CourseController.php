<?php

namespace App\Http\Controllers;

use App\Course;
use App\Temarios;
use App\Unit;
use App\Seccion;
use App\Files;
use App\User;
use App\Tests;
use App\Preguntas;
use App\Discusiones;
use App\Subscriptions;
use App\ComentariosDisc;
use Illuminate\Http\Request;
use App\Responses;
use App\Deliverable;
use App\Rating;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $c = \Auth::user();
        $cursos = Course::where('idUsuario','=',$c->id)->orderBy('id','DESC')->paginate(7);
        $currentDate = date('d/m/y');
        return view('pia.teach.courses.index',compact('cursos','currentDate'));
    }

    
    public function index1()
    {
        $cursos = self::getCourses();//Course::orderBy('id','desc')->paginate(7);
        $currentDate = date('d/m/y');
        $cn = new \App\Http\Controllers\SubscriptionsController();
        $currentUser = \Auth::user();
        return view('pia.std.cursos.mostrarCursos',compact('cn','cursos','currentDate','currentUser'));
    }

    public function isSuscribe($c,$u){
        return sizeof(Subscriptions::where('idUser','=',$c->id)->where('idUser','=',$u->id)->get())>0;
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

    public function iniciarCurso($id)
    {
        $curso = Course::find($id);
        $curso->estado=1;
        $curso->update();
        $currentDate = date('d/m/y');
        $temario = Temarios::where('idCurso','=',$curso->id)->get();
        $unidades = Unit::where('idCourse','=',$curso->id)->get();
        $students = Subscriptions::join('users','users.id','=','subscriptions.idUser')
            ->join('courses','courses.id','=','subscriptions.idCourse')
            ->where('idCourse','=',$curso->id)->get();
        return view('pia.teach.courses.iniciar',compact('curso','currentDate','temario','unidades','students'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currentDate = date('d/m/y');
        $msg='Curso creado correctamente.';
        $opt=0;
        return view('pia.teach.courses.create',compact('currentDate','opt','msg'));
    }



    public function createTemario($id)
    {
        $currentDate = date('d/m/y');
        $curso = Course::find($id);
        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;
        return view('pia.teach.courses.subirTemario',compact('result','currentDate','opt','msg','curso'));
    }



    public function crearUnidad($id)
    {
        $currentDate = date('d/m/y');
        $curso = Course::find($id);
        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;
        return view('pia.teach.courses.agregarUnidad',compact('result','currentDate','opt','msg','curso'));
    }

    public function agregarTemario(Request $request,$id)
    {
        
        
        if ($request->hasFile('temario')) {
           
            $file = $request->file('temario');
            //$fileName = time().$file->getClientOriginalName();
            $fileName = time().'_'.substr($file->getClientOriginalName(),0,6).'.pdf';
            $file->move(public_path().'/temarios',$fileName);

            $temario = new Temarios();
            $temario->idCurso=$id;
            $temario->nombre=$request->get('nombre');
            $temario->url=$fileName;
            $temario->save();
            
        }

        
        $curso = Course::find($id);
        $curso->estado=1;
        $curso->update();
        $currentDate = date('d/m/y');
        $temario = Temarios::where('idCurso','=',$curso->id)->get();
        $unidades = Unit::where('idCourse','=',$curso->id)->get();
        $students = Subscriptions::join('users','users.id','=','subscriptions.idUser')
            ->join('courses','courses.id','=','subscriptions.idCourse')
            ->where('idCourse','=',$curso->id)->get();
        return view('pia.teach.courses.iniciar',compact('curso','currentDate','temario','unidades','students'));
    }
    

    public function agregarUnidad(Request $request,$id)
    {
        
            $unidad = new Unit();
            $unidad->idCourse = $id;
            $unidad->name = $request->get('nombre');
            $unidad->initDate = $request->get('fecha_inicio');
            $unidad->endDate = $request->get('fecha_fin');
            $unidad->save();

        
        $curso = Course::find($id);
        $curso->estado=1;
        $curso->update();
        $currentDate = date('d/m/y');
        $temario = Temarios::where('idCurso','=',$curso->id)->get();
        $unidades = Unit::where('idCourse','=',$curso->id)->get();
        $students = Subscriptions::join('users','users.id','=','subscriptions.idUser')
            ->join('courses','courses.id','=','subscriptions.idCourse')
            ->where('idCourse','=',$curso->id)->get();
        return view('pia.teach.courses.iniciar',compact('curso','currentDate','temario','unidades','students'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $course = new Course();
        $currentUser = \Auth::user();        

        $course->idUsuario = $currentUser->id;
        $course->name = $request->get('nombre');        
        $course->credits = $request->get('creditos');
        $course->subject = $request->get('descripcion');        
        $course->level = $request->get('nivel');        
        $course->save();
        
        $currentDate = date('d/m/y');
        $msg='Curso creado correctamente.';
        $opt=1;
        return view('pia.teach.courses.create',compact('currentDate','opt','msg'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show($curso) // curso|unidad
    {
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
        
        $deliverables = Deliverable::where('idCourse','=',$idCurso)->where('idUnit','=',$idUnidad)->get();
        
        return view('pia.teach.courses.mostrarUnidad',compact('deliverables','curso','currentDate','unidad','cosas','secciones','archivos','tests','discusiones'));
    }

    #showSection    
    /**
     * Display the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function showSection($curso) 
    {
        
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
        return view('pia.teach.courses.viewSection',compact('curso','currentDate','unidad','cosas','secciones','archivos','tests','discusiones','section'));
    }


    public function crearSeccion($curso)
    {
        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;
        return view('pia.teach.courses.crearSeccion',compact('curso','currentDate','unidad','msg','opt','result'));
    }

    public function guardarSeccion(Request $r,$curso)
    {
        
        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);


        $seccion = new Seccion();
        $seccion->idCurso = $idCurso;
        $seccion->idUnidad = $idUnidad;
        $seccion->nombre = $r->get('nombre');
        $seccion->color = $r->get('color');
        $seccion->descripcion = $r->get('descripcion');
        $seccion->save();
        return redirect('mostrarUnidad/'.$idCurso.'|'.$idUnidad);
            
    }


    public function eliminarSeccion($curso){

        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idSeccion = explode('|',$curso)[2];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $seccion = Seccion::find($idSeccion);
        $seccion->delete();
        
        
        return redirect('mostrarUnidad/'.$idCurso.'|'.$idUnidad);
    }

    public function crearArchivo($curso)
    {
        $data =  explode('|',$curso);
        $section = new Seccion();
        $section->id=-1;
        if(sizeof($data)>2){
            $idSection = explode('|',$curso)[2];
            $section = Seccion::find($idSection);
        }

        
        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;
        return view('pia.teach.courses.crearArchivo',compact('curso','currentDate','unidad','msg','opt','result','section'));
    }

    public function guardarArchivo(Request $r,$curso)
    {
        
        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);

        $file = $r->file('archivo');
        $fileName = time().$file->getClientOriginalName();
        $file->move(public_path().'/files',$fileName);


        $section = $r->get('section');


        $archivo = new Files();
        $archivo->idCurso = $idCurso;
        $archivo->idUnidad = $idUnidad;
        $archivo->nombre = $r->get('nombre');
        $archivo->descripcion = $r->get('descripcion');
        $archivo->url = $fileName;
        $archivo->estado='OK'; 
        $archivo->idSection=$section; 
        $archivo->save();
        
        return redirect('mostrarUnidad/'.$idCurso.'|'.$idUnidad);
        
    }

    public function eliminarArchivo($curso){

        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idArchivo = explode('|',$curso)[2];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $archivo = Files::find($idArchivo);
        $archivo->delete();
        
        return redirect('mostrarUnidad/'.$idCurso.'|'.$idUnidad);
    }

    public function crearTest($curso)
    {

        $data =  explode('|',$curso);
        $section = new Seccion();
        $section->id=-1;
        if(sizeof($data)>2){
            $idSection = explode('|',$curso)[2];
            $section = Seccion::find($idSection);
        }

        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;
        return view('pia.teach.courses.crearTest',compact('section','curso','currentDate','unidad','msg','opt','result'));
    }

    public function guardarTest(Request $r,$curso)
    {
        
        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);

        

        $test = new Tests();
        $test->idCurso = $idCurso;
        $test->idUnidad = $idUnidad;
        $test->nombre = $r->get('nombre');
        $test->fecha = $r->get('fecha');
        $test->categoria = $r->get('categoria');
        $test->descripcion = $r->get('descripcion');
        $test->puntos = $r->get('puntos');
        $test->estado = 0;
        $test->revisado = 0;
        $test->idSection = $r->get('section');
        $test->save();
        
        
        return redirect('mostrarUnidad/'.$idCurso.'|'.$idUnidad);
        
        
    }


    public function eliminarTest($curso){

        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idTest = explode('|',$curso)[2];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $test = Tests::find($idTest);
        $test->delete();
        
        return redirect('mostrarUnidad/'.$idCurso.'|'.$idUnidad);

    }

    public function configurarTest($curso){

        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idTest = explode('|',$curso)[2];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $test = Tests::find($idTest);
        
        
        $secciones = Seccion::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $archivos = Files::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $tests = Tests::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $preguntas = Preguntas::where('idTest','=',$idTest)->get();
        $cosas = array();

        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;



        return view('pia.teach.courses.configurarTest',compact('curso','currentDate','unidad','test','opt','msg','result','preguntas'));

    }

    #crearPregunta
    public function crearPregunta($curso){ #idCurso|idUnidad|test

        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idTest = explode('|',$curso)[2];
        $tipo = explode('|',$curso)[3];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $test = Tests::find($idTest);
        
        
        $secciones = Seccion::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $archivos = Files::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $tests = Tests::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $preguntas = Preguntas::where('idTest','=',$idTest)->get();
        $cosas = array();

        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;



        return view('pia.teach.courses.crearPregunta',compact('curso','currentDate','unidad','test','opt','msg','result','preguntas','tipo'));

    }


    public function guardarPregunta(Request $r,$curso)
    {
        
        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idTest = explode('|',$curso)[2];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);

        

        $p = new Preguntas();
        $p->tipo = $r->get('tipo');
        $p->pregunta = $r->get('pregunta');
        $p->descripcion = $r->get('descripcion');
        $p->estado= 0;
        $p->valor = $r->get('valor');
        $p->idTest = $idTest;

        $check='0';
        if($p->tipo==1){
            
            $opciones = $r->get('verdadero').'|'.$r->get('falso');
            
            $correcta = $r->get('respuesta');


            if($correcta=='SI'){
                $respuesta = "SI|NO";
            }else{
                $respuesta = "NO|SI";
            }
            $p->opciones = $opciones;
            $p->respuestas = $respuesta;
        }else if($p->tipo==2){
            $opciones = 'NO APLICA';
            $respuesta = $r->get('respuesta');
            $p->opciones = $opciones;
            $p->respuestas = $respuesta;
        }else if($p->tipo==3){

            $op = $r->get('opciones');
            $re = $r->get('posibles');

            $opciones='';
            $respuesta ='';
            foreach ($op as $v) {
                $opciones = $opciones.'|'.$v;
            }

            foreach ($re as $v) {
                $respuesta = $respuesta.'|'.$v;
            }
            
            $p->opciones = substr($opciones,1);
            $p->respuestas = substr($respuesta,1);    
        }        
        $p->save();
        
        $secciones = Seccion::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $archivos = Files::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $tests = Tests::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $preguntas = Preguntas::where('idTest','=',$idTest)->get();
        $cosas = array();
        $test = Tests::find($idTest);
        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;



        return view('pia.teach.courses.configurarTest',compact('curso','currentDate','unidad','test','opt','msg','result','preguntas'));
        
    }

    public function eliminarPregunta($curso){

        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idTest = explode('|',$curso)[2];
        $idPregunta = explode('|',$curso)[3];


        $p = Preguntas::find($idPregunta);
        $p->delete();
        
        


        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $test = Tests::find($idTest);
        $secciones = Seccion::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $archivos = Files::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $tests = Tests::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $preguntas = Preguntas::where('idTest','=',$idTest)->get();
        $cosas = array();
        $test = Tests::find($idTest);
        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;



        return view('pia.teach.courses.configurarTest',compact('curso','currentDate','unidad','test','opt','msg','result','preguntas'));
    }


    public function mostrarPregunta($curso){ 

        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idTest = explode('|',$curso)[2];
        $idPregunta = explode('|',$curso)[3];
        
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $test = Tests::find($idTest);
        $pregunta = Preguntas::find($idPregunta);
        
        
        $secciones = Seccion::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $archivos = Files::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $tests = Tests::where('idCurso','=',$idCurso)->where('idUnidad','=',$idUnidad)->get();
        $cosas = array();

        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;



        return view('pia.teach.courses.mostrarPregunta',compact('curso','currentDate','unidad','test','opt','msg','result','pregunta'));

    }


    #

    public function crearDiscusion($curso){ 

        $data =  explode('|',$curso);
        $section = new Seccion();
        $section->id=-1;
        if(sizeof($data)>2){
            $idSection = explode('|',$curso)[2];
            $section = Seccion::find($idSection);
        }
        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;
        return view('pia.teach.courses.crearDiscusion',compact('section','curso','currentDate','unidad','opt','msg','result'));

    }


    public function guardarDiscusion(Request $r, $curso){

        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        


        $d = new Discusiones();
        $d->titulo = $r->get('titulo');
        $d->descripcion = $r->get('descripcion');
        $d->fecha = $r->get('fecha');
        $d->estado = 1;
        $d->idCurso = $idCurso;
        $d->idUnidad = $idUnidad;
        $d->idSection = $r->get('section');
        $d->save();

        return redirect('mostrarUnidad/'.$idCurso.'|'.$idUnidad);

    }


    public function eliminarDiscusion($curso){

        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idDiscusion = explode('|',$curso)[2];
        $p = Discusiones::find($idDiscusion);
        $p->delete();

        return redirect('mostrarUnidad/'.$idCurso.'|'.$idUnidad);
    }


    public function mostrarDiscusion($curso){ 

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
        return view('pia.teach.courses.mostrarDiscusion',compact('curso','currentDate','unidad','discusion','opt','msg','result','comentarios'));

    }


    public function guardarComentarioDiscusion(Request $r,$curso){
        $currentDate = date('d/m/y');
        $idCurso = explode('|',$curso)[0];
        $idUnidad = explode('|',$curso)[1];
        $idDiscusion = explode('|',$curso)[2];
        $currentUser = \Auth::user();

        $c = new ComentariosDisc();
        $c->idUsuario = $currentUser->id;
        $c->idDiscusion = $idDiscusion;
        $c->comentario= $r->get('comentario');
        $c->fecha = date('y/m/d');
        $c->estado=0;
        $c->save();


        
        
        
        $comentarios = ComentariosDisc::where('idDiscusion','=',$idDiscusion)->get();
        $curso = Course::find($idCurso);
        $unidad = Unit::find($idUnidad);
        $discusion = Discusiones::find($idDiscusion);
        $msg='Curso creado correctamente.';
        $opt=0;
        $result=0;
        return view('pia.teach.courses.mostrarDiscusion',compact('curso','currentDate','unidad','discusion','opt','msg','result','comentarios'));



    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $currentDate = date('d/m/y');
        $msg='Curso creado correctamente.';
        $opt=0;
        $curso = Course::find($id);
        return view('pia.teach.courses.edit',compact('currentDate','opt','msg','curso'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $course = Course::find($id);
        $currentUser = \Auth::user();        

        $course->idUsuario = $currentUser->id;
        $course->name = $request->get('nombre');        
        $course->credits = $request->get('creditos');
        $course->subject = $request->get('descripcion');        
        $course->level = $request->get('nivel');        
        $course->save();
        
        $cursos = Course::orderBy('id','DESC')->paginate(7);
        $currentDate = date('d/m/y');
        return view('pia.teach.courses.index',compact('cursos','currentDate'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = Course::find($id);
        $course->delete();
        $cursos = Course::orderBy('id','DESC')->paginate(7);
        $currentDate = date('d/m/y');
        return view('pia.teach.courses.index',compact('cursos','currentDate'));
    }

    public function destroyTemario($id)
    {
        $temario = Temarios::where('idCurso','=',$id)->get()->first();
        $temario->delete();
        return redirect('iniciarCurso/'.$id);
    }


    public function eliminarUnidad($id)
    {
            
        $idCurso = explode('|',$id)[0];
        $idUnidad = explode('|',$id)[1];
        
        $temario = Unit::find($idUnidad);
        $temario->delete();
        
        return redirect('iniciarCurso/'.$idCurso);
        
    }

    public function getStudentStatus($id){
        return 'OK';
    }

    public function reviewHomeWork($id){
        $deliverable = Deliverable::find($id);
        $student = User::find($deliverable->idUser);
        $course = Course::find($deliverable->idCourse);
        $unit = Unit::find($deliverable->idUnit);

        if($deliverable->type==1){ #test
            $test = Tests::find($deliverable->idDeliverable);
            $contentTest = Preguntas::where('idTest','=',$test->id)->get();
            //$correct = explode('|',);
            $responses = Responses::where('idTest','=',$test->id)
                        ->where('idUser','=',$student->id)
                        ->get();

        }else{ #tareas

        }
        return view('pia.teach.courses.reviewDeliverable',compact('deliverable','student','course','unit','test','contentTest','responses'));
    }
    
    public function qualify(Request $data,$id){ //idDeliverable|Student|Type

        $score = $data->input('score');
        $idDeliverable = explode('|',$id)[0];
        $type = explode('|',$id)[2];
        $student = explode('|',$id)[1];
        $rating = new Rating();
        $rating->score = $score;
        $rating->state=0;
        if($type=='T'){
            $rating->idDeliverable = $idDeliverable;
            $rating->idUser = $student;
            $rating->typeDeliverable = 1;
            
        }else {
         
        }
        $rating->save();
        $deliverable = Deliverable::find($idDeliverable);
        $deliverable->isCheck=1;
        $deliverable->update();
        return 'OK';
    }
}
