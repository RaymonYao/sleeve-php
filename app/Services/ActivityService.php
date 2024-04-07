<?php

namespace App\Services;

use App\Models\ActivityModel;
use Exception;

class ActivityService
{
    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function getActivityByName(string $name)
    {
        if (empty($name)) throw new Exception('name参数错误');
        return ActivityModel::where('name', $name)->first();
    }
}
