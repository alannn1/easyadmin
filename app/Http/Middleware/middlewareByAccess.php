<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Idev\EasyAdmin\app\Helpers\Constant;

class middlewareByAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()->getName();
        $allowAccess = (new Constant())->permissions();
        
        return in_array($routeName, $allowAccess['list_access']) ? $next($request) : abort(404);
    }
}
