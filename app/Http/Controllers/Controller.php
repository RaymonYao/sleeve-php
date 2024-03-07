<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    const SUCCESS = 100;
    const FAIL = 900;

    /**
     * @param string|int|null $key
     * @return mixed
     */
    protected function getJsonPost($key = null)
    {
        $res = json_decode(app()->get(Request::class)->getContent(), true);
        if (!is_array($res)) return null;
        return $key !== null ? ($res[$key] ?? null) : $res;
    }
}
