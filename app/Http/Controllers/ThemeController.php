<?php

namespace App\Http\Controllers;

use App\Services\ThemeService;
use Exception;
use Illuminate\Http\JsonResponse;

class ThemeController extends Controller
{

    protected $themeService;

    /**
     * TicketController constructor.
     * @param ThemeService $themeService
     */
    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function list(): JsonResponse
    {
        return response()->json([
            'code' => static::SUCCESS,
            'data' => $this->themeService->getThemeList(request()->input('names'))
        ]);
    }
}
