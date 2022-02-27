<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckApiAuth
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
  
            $username = $request->header('username') ;
    
            $password = $request->header('password');

            //Log::info($request);
            $record = DB::table('users')->where('email', $username)->orWhere('email', $username)->first();
            $email = $record !=null ? $record->email : "";
            //Log::info($email);
        
            

            $credential = ['email'=>$email, 'password' =>$password];


            if (auth()->once($credential)) {

                return $next($request);
            }else {
                return response()->json(
                    [
                        'message' => 'Authentication Failed',
                        'status' => '300'
                    ]
                );

            }

        
    }
}