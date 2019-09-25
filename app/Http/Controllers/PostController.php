<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentDate = date('Y-m-d');
        $posts = Post::orderBy('created_at','desc')->paginate(10);
        return view('pia.admin.posts.index',compact('currentDate','posts'));
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
        return view('pia.admin.posts.create',compact('currentDate','result','msg'));
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
        if ($request->hasFile('image')) {
            $result = 1;
            $msg = 'Publicación agregada correctamente';
            $currentUser = \Auth::user();
            $post = new Post();
            $post->idUser = $currentUser->id;
            $file = $request->file('image');
            $fileName = time().$file->getClientOriginalName();
            $file->move(public_path().'/posts',$fileName);


            $post->img = $fileName;
            $post->title = $request->input("title");
            $post->summary = $request->input("summary");
            $post->introduction = $request->input("introduction"); 
            $post->content = $request->input("content");
            $post->conclusion = $request->input("conclusion");
            $resultQuery= $post->save();

            if ($resultQuery) {
                return view('pia.admin.posts.create',compact('currentDate','result','msg'));    
            } 
            $result = 2;
            
        }
        
        if($result==2){
            $msg = 'Oh no! Ha ocurrido un error inseperado!';
            return view('pia.admin.posts.create',compact('currentDate','result','msg'))    
            ->withInput($request);
        }

        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        return view('pia.admin.posts.show',compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $currentDate = date('Y-m-d');
        $msg = 'Oh no! Ha ocurrido un error inseperado!';
        $result =-1;
        $post = Post::find($id);
        return view('pia.admin.posts.edit',compact('currentDate','result','msg','post'));
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
        $post = Post::find($id);
        $msg = 'Publicación actualizada correctamente';
        $currentUser = \Auth::user();
        $post->idUser = $currentUser->id;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time().$file->getClientOriginalName();
            $file->move(public_path().'/posts',$fileName);
            $post->img = $fileName;
        }
        $post->title = $request->input("title");
        $post->summary = $request->input("summary");
        $post->introduction = $request->input("introduction"); 
        $post->content = $request->input("content");
        $post->conclusion = $request->input("conclusion");
        $resultQuery= $post->update();

        if ($resultQuery) {
            $currentDate = date('Y-m-d');
            $posts = Post::orderBy('created_at','desc')->paginate(10);
            return view('pia.admin.posts.index',compact('currentDate','posts'));
        }else{
            $msg = 'Oh no! Ha ocurrido un error inseperado!';
            return view('pia.admin.posts.edit',compact('currentDate','result','msg','post'))    
            ->withInput($request);
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
        $currentDate = date('Y-m-d');
        $post = Post::find($id);
        $result = $post->delete();
        $posts = Post::orderBy('created_at','desc')->paginate(10);
        return view('pia.admin.posts.index',compact('currentDate','posts'));
    }
}
