<?php

namespace App\Services;

use App\Models\SpuModel;
use App\Models\ThemeModel;
use Exception;

class SpuService
{
    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function getSpuListByTheme(string $name)
    {
        if (empty($name)) throw new Exception('name参数错误');
        $theme = ThemeModel::where('name', $name)->first();
        return [
            'spu_list' => SpuModel::where('theme_id', $theme->id)->get()
        ];
    }
}
