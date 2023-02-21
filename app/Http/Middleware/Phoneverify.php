<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Phoneverify
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
        if(auth()->user()->phone_number_status) {
            return $next($request);
        }else {
            return response()->json([
                'message' => 'Your phone number is not verified',
                'error' => true,
                'data' => null
            ], 400);
        }
    }
}