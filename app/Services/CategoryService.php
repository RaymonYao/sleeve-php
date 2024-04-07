<?php

namespace App\Services;

use App\Models\CategoryModel;
use Exception;

class CategoryService
{
    /**
     * @return mixed
     * @throws Exception
     */
    public function getGridAll()
    {
        return CategoryModel::get();
    }
}
