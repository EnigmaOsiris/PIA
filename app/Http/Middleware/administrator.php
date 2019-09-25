<?php

namespace App\Http\Middleware;

use Closure;

class administrator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $currentUser = \Auth::user();
        if(isset($currentUser)){
            if($currentUser->userType==1 || $currentUser->userType==2){
                return $next($request);    
            }
        }
        return redirect('/');
        
        
    }
}
