<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('api_key') ;
        
        $keyCheck = DB::table('api_key')->where('api_key', $apiKey);
        
        if($keyCheck->exists())
        {
            return $next($request);
            
        }else {
            return response()->json(
                [
                    'message' => 'Invalid Key',
                    'status' => '300'
                ]
            );

        }
    
    }
}
