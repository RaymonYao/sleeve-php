<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use Exception;
use FastRoute\Route;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{

    protected $activityService;

    /**
     * TicketController constructor.
     * @param ActivityService $activityService
     */
    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function getActivity(): JsonResponse
    {
        return response()->json([
            'code' => static::SUCCESS,
            'data' => $this->activityService->getActivityByName(pathinfo(request()->path())['basename'])
        ]);
    }
}
