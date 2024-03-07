<?php

namespace App\Http\Middleware;

use App\Models\UserModel;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Jwt
{
    const NOW_LOGIN_USER = 'now_login_user';

    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            /** @var JwtService $jwtService */
            $jwtService = app()->make(JwtService::class);
            $payload = $jwtService->getPayLoad($request->header('accessToken'));
            if (empty($payload->exp) || $payload->exp < time()) {
                throw new \Exception('token expired');
            }
            $uObj = UserModel::where('uid', $payload->uid)->first();
            app()->bind(static::NOW_LOGIN_USER, function () use ($uObj) {
                return $uObj;
            });
        } catch (\Exception $e) {
            return response()->json([
                'code' => Response::HTTP_UNAUTHORIZED,
                'msg' => 'token error: ' . $e->getMessage()
            ]);
        }
        return $next($request);
    }
}
