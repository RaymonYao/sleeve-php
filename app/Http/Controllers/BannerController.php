<?php

namespace App\Http\Controllers;

use App\Services\bannerService;
use Exception;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{

    protected $bannerService;

    /**
     * TicketController constructor.
     * @param BannerService $bannerService
     */
    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function getBanner(): JsonResponse
    {
        return response()->json([
            'code' => static::SUCCESS,
            'data' => $this->bannerService->getBannerList(pathinfo(request()->path())['basename'])
        ]);
    }
}
