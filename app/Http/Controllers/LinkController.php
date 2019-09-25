<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Link;
use App\SectionLinks;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $links = Link::orderBy('created_at','desc')->paginate(10);
        $sections = SectionLinks::orderBy('name','desc')->getQuery()->get();
        $currentDate = date('Y-m-d');
        return view('pia.admin.library.index',compact('links','sections','currentDate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createSection()
    {
        $currentDate = date('Y-m-d');
        $result = 0;
        $msg = 'Agregado correctamente';
        return view('pia.admin.library.createSection',compact('currentDate','msg','result'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currentDate = date('Y-m-d');
        $result = 0;
        $msg = 'Agregado correctamente';
        $sections = SectionLinks::orderBy('name','desc')->getQuery()->get();
        if(count($sections)<=0){
            $result = 2;
            $msg = "Aún no ha agrefado secciones... Comience por agregar una sección.";
        }
        return view('pia.admin.library.create',compact('currentDate','msg','result','sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSection(Request $request)
    {
        $currentDate = date('Y-m-d');
        $result=2;
        
        $currentUser = \Auth::user();
        $section = new SectionLinks();
        $section->name = $request->input('name');
        $section->description = $request->input('description');
        $result = $section->save();

        if($result){
        $result = 1;
        $msg = 'Sección agregada correctamente';
        $result=1;
        }else{
            $msg = 'Sección agregada correctamente';
        }
        return view('pia.admin.library.createSection',compact('currentDate','msg','result'));
    }

/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $currentDate = date('Y-m-d');
        $result=2;
        $currentUser = \Auth::user();
        $link = new Link();
        $link->idSection = $request->input('idSection');
        $link->idUser = $currentUser->id;
        $link->url = $request->input('url');
        $link->imgUrl = $request->input('urlImg');
        $link->name = $request->input('name');
        $link->description = $request->input('description');
        $link->code="x";
        $result = $link->save();

        if($result){
        $result = 1;
        $msg = 'Enlace agregado correctamente';
        $result=1;
        }else{
            $msg = 'Ha ocurrido un error inesperado. Vuelva a intentarlo.';
        }

        $sections = SectionLinks::orderBy('name','desc')->getQuery()->get();
        if(count($sections)<=0){
            $result = 2;
            $msg = "Aún no ha agrefado secciones... Comience por agregar una sección.";
        }

        $links = Link::orderBy('created_at','desc')->paginate(10);
        $sections = SectionLinks::orderBy('name','desc')->getQuery()->get();
        $currentDate = date('Y-m-d');
        return view('pia.admin.library.index',compact('links','sections','currentDate'));

       // return view('pia.admin.library.create',compact('currentDate','msg','result','sect7ions'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editSection($id)
    {
        $currentDate = date('Y-m-d');
        $result = 0;
        $msg = 'Agregado correctamente';
        $section = SectionLinks::find($id);
        return view('pia.admin.library.editSection',compact('currentDate','msg','result','section'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $link = Link::find($id);
        $currentDate = date('Y-m-d');
        $result = 0;
        $msg = 'Agregado correctamente';
        $sections = SectionLinks::find($id);
        return view('pia.admin.library.edit',compact('currentDate','msg','result','sections','link'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSection(Request $request, $id)
    {
        $section = SectionLinks::find($id);
        $section->name = $request->input('name');
        $section->description = $request->input('description');
        $result = $section->update();

        if($result){
            $links = Link::orderBy('created_at','desc')->paginate(10);
            $sections = SectionLinks::orderBy('name','desc')->getQuery()->get();
            $currentDate = date('Y-m-d');
            return view('pia.admin.library.index',compact('links','sections','currentDate'));
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $link = Link::find($id);
        $link->idSection = $request->input('idSection');
        $link->idUser = $request->input('idSection');
        $currentUser = \Auth::user();
        $link->idUser = $currentUser->id;
        $link->url = $request->input('url');
        $link->imgUrl = $request->input('urlImg');
        $link->name = $request->input('name');
        $link->description = $request->input('description');
        $link->code="x";
        $result = $link->update();

        if($result){
            $links = Link::orderBy('created_at','desc')->paginate(10);
            $sections = SectionLinks::orderBy('name','desc')->getQuery()->get();
            $currentDate = date('Y-m-d');
            return view('pia.admin.library.index',compact('links','sections','currentDate'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $link = Link::find($id);
        $link->delete();
        $links = Link::orderBy('created_at','desc')->paginate(10);
        $sections = SectionLinks::orderBy('name','desc')->getQuery()->get();
        $currentDate = date('Y-m-d');
        return view('pia.admin.library.index',compact('links','sections','currentDate'));
    }
}
