<?php

namespace App\Http\Controllers;

use App\Services\SpuService;
use Exception;
use Illuminate\Http\JsonResponse;

class SpuController extends Controller
{

    protected $themeService;

    /**
     * SpuController constructor.
     * @param SpuService $spuService
     */
    public function __construct(SpuService $spuService)
    {
        $this->spuService = $spuService;
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function getSpuListByTheme(): JsonResponse
    {
//        dd(pathinfo(pathinfo(request()->path())['dirname'])['basename']);
        return response()->json([
            'code' => static::SUCCESS,
            'data' => $this->spuService->getSpuListByTheme(pathinfo(pathinfo(request()->path())['dirname'])['basename'])
        ]);
    }

    public function getLatestSpu(): JsonResponse
    {
        return response()->json([
            'code' => static::SUCCESS,
            'data' => $this->spuService->getLatestSpu(request()->input())
        ]);
    }
}
