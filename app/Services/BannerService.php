<?php

namespace App\Services;

use App\Models\BannerModel;
use Exception;

class BannerService
{
    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function getBannerList(string $name)
    {
        if (empty($name)) throw new Exception('name参数错误');
        $parentBanner = BannerModel::where('name', $name)->first();
        $bannerList = BannerModel::where('parent_id', $parentBanner->id)->get();
        return [
            'id' => $parentBanner->id,
            'name' => $parentBanner->name,
            'description' => $parentBanner->description,
            'img' => $parentBanner->img,
            'title' => $parentBanner->title,
            'items' => $bannerList
        ];
    }
}
