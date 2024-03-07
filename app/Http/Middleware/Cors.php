<?php

/**
 * 跨域设置
 */

namespace App\Http\Middleware;


use Laravel\Lumen\Http\Request;
use Closure;

class Cors
{
    /**
     * 跨域
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array(env('APP_ENV'), ['test', 'local'])) {
            header('Access-Control-Allow-Origin: *');
        }
        return $next($request);
    }
}
