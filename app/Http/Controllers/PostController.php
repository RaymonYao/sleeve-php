<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{

    protected $postService;

    /**
     * TicketController constructor.
     * @param PostService $postService
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function list()
    {
        return response()->json([
            'code' => static::SUCCESS,
            'data' => [
                'postList' => $this->postService->getPostList($this->getJsonPost('ticket_id'))
            ]
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function reply()
    {
        try {
            $params = $this->getJsonPost();
            Validator::make($params, [
                'ticket_id' => 'required',
                'parent_id' => 'integer',
                'content' => 'required'
            ], [
                'required' => ':attribute必填',
                'integer' => ':attribute必须为整型'
            ])->validate();
            $params['attachment'] = json_encode($params['attachment'] ?? []);
            $this->postService->reply($params);
            return response()->json([
                'code' => static::SUCCESS,
                'msg' => '操作成功'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => static::FAIL,
                'msg' => '保存失败',
                'data' => $e->errors()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => static::FAIL,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
