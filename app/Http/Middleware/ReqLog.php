<?php

/**
 * 日志
 */

namespace App\Http\Middleware;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Log;

class ReqLog
{
    /**
     * 跨域
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $logStr = '--url:' . $request->getRequestUri() .
            '  --request: ' . json_encode($request->all()) .
            '  --headers: '. json_encode($request->header()).
            '  --content: ' . $request->getContent();
        $response = $next($request);
        if ($response instanceof JsonResponse) {
//            Log::debug($logStr . '  --response:' . $response->getContent());
            $data = json_decode($response->getContent(), true);
            $data['data'] = 'ignore';
            Log::debug($logStr . '  --response:' . json_encode($data));
        }
        return $response;
    }
}
