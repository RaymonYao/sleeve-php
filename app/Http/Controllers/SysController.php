<?php

namespace App\Http\Controllers;


use App\Services\AliOssService;
use App\Services\SysService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SysController extends Controller
{
    protected $sysService;

    public function __construct(SysService $sysService)
    {
        $this->sysService = $sysService;
    }

    /**
     * @return JsonResponse
     */
    public function deptList()
    {
        return response()->json([
            'code' => static::SUCCESS,
            'data' => [
                'deptList' => $this->sysService->getDeptList($this->getJsonPost('need_user') ?? 0)
            ]
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function userList()
    {
        return response()->json([
            'code' => static::SUCCESS,
            'data' => [
                'userList' => $this->sysService->getUserList()
            ]
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            $img = $this->getJsonPost('img');   //前端定义好key 为img
            $file = $request->file('file');    //前端定义好key 为file
            if (empty($file) && empty($img)) {
                throw new Exception('上传文件不能为空');
            }
            if ($file && $file->getError() !== 0) {
                throw new Exception('上传文件发生错误,' . $file->getErrorMessage());
            }
            return response()->json([
                'code' => static::SUCCESS,
                'url' => (new AliOssService())->putObject(!empty($img) ? $img : $file)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => static::FAIL,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
