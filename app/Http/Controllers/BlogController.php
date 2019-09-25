<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
class BlogController extends Controller
{
    public function index(){
        $posts = Post::orderBy('created_at','desc')->paginate(10);
        return  view('blog.index',compact('posts'));
    }

    public function show($id){
        $post = Post::find($id);
        return view('blog.show',compact('post'));
    }
}
