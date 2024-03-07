<?php

namespace App\Http\Controllers;

use App\Services\OpLogService;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

class TicketController extends Controller
{
    /**
     * @var TicketService
     */
    protected $ticketService;

    /**
     * @var OpLogService
     */
    protected $opLogService;

    /**
     * TicketController constructor.
     * @param TicketService $ticketService
     * @param OpLogService $opLogService
     */
    public function __construct(TicketService $ticketService, OpLogService $opLogService)
    {
        $this->ticketService = $ticketService;
        $this->opLogService = $opLogService;
    }

    /**
     * 查询工单列表
     * @return JsonResponse
     */
    public function list()
    {
        return response()->json([
            'code' => static::SUCCESS,
            'data' => [
                'ticketList' => $this->ticketService->getTicketList($this->getJsonPost())
            ]
        ]);
    }

    /**
     * 保存工单
     * @return JsonResponse
     */
    public function save()
    {
        try {
            $params = $this->getJsonPost();
            Validator::make($params, [
                'title' => 'required|max:255',
                'content' => 'required',
                'expect_finish_at' => 'required|date'
            ], [
                'required' => ':attribute必填',
                'max' => ':attribute长度不能大于:max',
                'date' => ':attribute不是合法的日期时间格式'
            ])->validate();
            $params['attachment'] = json_encode($params['attachment'] ?? []);
            $this->ticketService->saveTicket($params);
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

    /**
     * 查询工单详情
     * @return JsonResponse
     */
    public function detail()
    {
        try {
            return response()->json([
                    'code' => static::SUCCESS,
                    'data' => [
                        'ticketDetail' => $this->ticketService->getTicketDetail($this->getJsonPost('ticket_id'))
                    ]
                ]
            );
        } catch (Exception $e) {
            return response()->json([
                'code' => static::FAIL,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 取消工单
     * @return JsonResponse
     */
    public function cancel()
    {
        try {
            $this->ticketService->cancelTicket($this->getJsonPost('ticket_id'));
            return response()->json([
                'code' => static::SUCCESS,
                'msg' => '操作成功'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => static::FAIL,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 指派, 再指派工单
     * @return JsonResponse
     */
    public function assign()
    {
        try {
            $params = $this->getJsonPost();
            Validator::make($params, [
                'ticket_id' => 'required|integer',
                'assign_to_dept' => 'required|integer',
                'assign_to' => 'required',
            ], [
                'required' => ':attribute必填',
                'integer' => ':attribute必须为数字',
            ])->validate();
            $this->ticketService->assignTicket($params);
            return response()->json([
                'code' => static::SUCCESS,
                'msg' => '操作成功'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => static::FAIL,
                'msg' => '操作失败',
                'data' => $e->errors()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => static::FAIL,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 发起人/承接人 完成
     * @return JsonResponse
     */
    public function complete()
    {
        try {
            $this->ticketService->completeTicket($this->getJsonPost('ticket_id'));
            return response()->json([
                'code' => static::SUCCESS,
                'msg' => '操作成功'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => static::FAIL,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 催办
     * @return JsonResponse
     */
    public function urge()
    {
        try {
            $this->ticketService->urgeTicket($this->getJsonPost('ticket_id'));
            return response()->json([
                'code' => static::SUCCESS,
                'msg' => '操作成功'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => static::FAIL,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 查询操作日志
     * @return JsonResponse
     */
    public function getOpLogList()
    {
        return response()->json([
            'code' => static::SUCCESS,
            'data' => [
                'ticketList' => $this->opLogService->getLog($this->getJsonPost('ticket_id'))
            ]
        ]);
    }
}
