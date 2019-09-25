<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use Session;
class Users extends Controller
{


    public function __construct(){

    }


    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
       
    }

    public function showLogingForm()
    {
        return view('auth.login');
    }

    public function checkLoging()
    {

        #Validar el formulario
        $credentials = $this->validate(#Utilizamos la funcion validate de Controller que retorna un arreglo con la informacion validad
            request(),[
                'email'=>'email|required|string',
                'password'=>'required|string'
            ]);
        #Utilizaremos el metodo attempt de Auth que verifica en la tabla usuarios si existe un usuario con esas credenciales
        if(Auth::attempt($credentials)){ #Recibe un array 
            
            $currentUser = \Auth::user();    

            if($currentUser->userType==1|| $currentUser->userType==2)
            {
                return redirect()->route('admin');
            }else if($currentUser->userType==3){
                return redirect()->route('teach');
            }else{
                return redirect()->route('std');
            }
            
        }else{
            #Si no esta rgistrado, regresamos con los datos y el error referenciando al mensaje que se encuentra en el archivo auth.failed
            return back()->withErrors(['email'=>trans('auth.failed')])
            ->withInput(request(['email']));
        }
                


        //return view('auth.login');
    }

    public function reset(){
        return view('auth.passwords.reset');
    }

    public function request(){
        return view('auth.passwords.email');
    }

    public function showRegisterForm(){   
        return view('auth.register');
    }

    public function saveUser(){
        
        $data = $this->validate( 
            request(),[
                'email'=>'email|required|string',
                'password'=>'required|string',
                'password_confirmation'=>'required|string',
                'lastnames'=>'required|string|max:50',
                'usertype'=>'required',
                'career'=>'required|string',
                'name'=>'required|string|max:50|alpha'
            ]);
       
        $userInformation = new User();    
        $userInformation->names = $data['name'];
        $userInformation->email= $data['email'];
        $userInformation->password = bcrypt($data['password']);
        $userInformation->userType= $data['usertype'];
        $userInformation->career = $data['career'];
        $userInformation->lastNames = $data['lastnames'];
        $userInformation->phone = '';
        $userInformation->institute = '';
        $result = $userInformation->save();

        if($result){
            return redirect()->route('login'); //->with("msj","Usuario Registrado Correctamente"); 
            //return view("auth.login")->with("msj","Usuario Registrado Correctamente");   
            
        }else{
            return back()->withErrors(['email'=>trans('auth.failed')])
            ->withInput(request(['email']));
        }
        
        
    }
    public function logout(){
        #Solo invocamos al metod  logout de auth
        Auth::logout();
       // $this->auth->logout();
        Session::flush();
        return redirect('/'); #Redireccionamos a la raiz
    }

    public function showGeneral(){
        return view('pia.accounts.general');
    }


}
