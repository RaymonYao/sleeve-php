<?php

namespace App\Http\Controllers;

use App\Services\JwtService;
use App\Services\DingTalkService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    /**
     * @var DingTalkService
     */
    protected $ddService;

    protected $jwt;

    /**
     * Create a new controller instance.
     *
     * @param DingTalkService $ddService
     */
    public function __construct(DingTalkService $ddService, JwtService $jwt)
    {
        $this->ddService = $ddService;
        $this->jwt = $jwt;
    }

    public function login(Request $request, $code)
    {
        try {
            $userInfo = $this->ddService->loginDingTalk($code, $request->get('env', 0));
            return response()->json([
                'code' => static::SUCCESS,
                'data' => [
                    'uInfo' => $userInfo,
                    'accessToken' => $this->jwt->getToken($userInfo)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => Response::HTTP_UNAUTHORIZED,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
