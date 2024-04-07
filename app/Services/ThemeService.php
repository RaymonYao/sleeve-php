<?php

namespace App\Services;

use App\Models\ThemeModel;
use Exception;

class ThemeService
{
    /**
     * @param string $names
     * @return mixed
     * @throws Exception
     */
    public function getThemeList(string $names)
    {
        if (empty($names)) throw new Exception('names参数错误');
        return ThemeModel::whereIn('name', explode(',', $names))->get();
    }
}
