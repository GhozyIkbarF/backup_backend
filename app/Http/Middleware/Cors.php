<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);
        $response = $next($request);
        $response = response($response);

        $response->header('Access-Control-Allow-Origin', 'http://localhost:3000');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');
        $response->header('Access-Control-Allow-Headers', 'Origin, Accept, Content-Type, Authorization, X-Requested-With');
        // $response->header('Access-Control-Allow-Credentials', 'true');
        
        return response()->json($response->original->original);
    }
}
