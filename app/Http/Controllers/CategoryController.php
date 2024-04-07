<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Exception;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{

    protected $categoryService;

    /**
     * CategoryController constructor.
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function getCategory(): JsonResponse
    {
        return response()->json([
            'code' => static::SUCCESS,
            'data' => $this->categoryService->getGridAll()
        ]);
    }
}
