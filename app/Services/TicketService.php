<?php

namespace App\Services;

use App\Http\Middleware\Jwt;
use App\Models\TicketModel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TicketService
{
    /**
     * @var OpLogService
     */
    protected $opLogService;

    /**
     * @var MsgService
     */
    protected $msgService;

    /**
     * @var OpPermissionService
     */
    protected $opPermissionService;

    public function __construct(OpLogService $opLogService, MsgService $msgService, OpPermissionService $opPermissionService)
    {
        $this->opLogService = $opLogService;
        $this->msgService = $msgService;
        $this->opPermissionService = $opPermissionService;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getTicketList(array $params)
    {
        $ticketModel = TicketModel::select([
            'ticket_id', 'title', 'expect_finish_at', 'created_by', 'created_by_dept', 'assign_to',
            'assign_to_dept', 'final_handler', 'final_handler_dept', 'status', 'finish_at', 'created_at', 'updated_at'
        ])->with(['createdUser:uid,username', 'assignToUser:uid,username','finalHandlerUser:uid,username']);
        if (!empty($params['tab'])) {
            $userObj = app()->get(Jwt::NOW_LOGIN_USER);
            switch ($params['tab']) {//0:全部工单 1:我发起的 2:我部门发起的 3:指派给我的 4:指派给我部门的
                case 1: //
                    $ticketModel->where('created_by', $userObj->uid);
                    break;
                case 2:
                    $ticketModel->where('created_by_dept', $userObj->getDeptByLevel()->dept_id);
                    break;
                case 3:
                    $ticketModel->where('assign_to', $userObj->uid);
                    break;
                case 4:
                    $ticketModel->where('assign_to_dept', $userObj->getDeptByLevel()->dept_id);
                    break;
            }
        }
        if (!empty($params['title'])) {
            $ticketModel->where('title', 'like', '%' . $params['title'] . '%');
        }
        if (!empty($params['status'])) {
            $ticketModel->where('status', $params['status']);
        }
        if (!empty($params['created_by'])) {
            $ticketModel->where('created_by', $params['created_by']);
        }
        if (!empty($params['assign_to'])) {
            $ticketModel->where('assign_to', $params['assign_to']);
        }
        if (!empty($params['final_handler'])) {
            $ticketModel->where('final_handler', $params['final_handler']);
        }
        if (!empty($params['created_by_dept'])) {
            $ticketModel->where('created_by_dept', $params['created_by_dept']);
        }
        if (!empty($params['assign_to_dept'])) {
            $ticketModel->where('assign_to_dept', $params['assign_to_dept']);
        }
        if (!empty($params['final_handler_dept'])) {
            $ticketModel->where('final_handler_dept', $params['final_handler_dept']);
        }
        if (!empty($params['start_date'])) {
            $ticketModel->where('created_at', '>=', $params['start_date'][0])
                ->where('created_at', '<', date('Y-m-d', strtotime($params['start_date'][1] . ' +1 day')));
        }
        if (!empty($params['end_date'])) {
            $ticketModel->where('finish_at', '>=', $params['end_date'][0])
                ->where('finish_at', '<', date('Y-m-d', strtotime($params['end_date'][1] . ' +1 day')));
        }
        if (!empty($params['is_overdue'])) {
            if ($params['is_overdue'] == TicketModel::O_OVERDUE) {
                $ticketModel->whereRaw('((finish_at is null and DATEDIFF(expect_finish_at,CURRENT_DATE()) < 0)
                or (finish_at is not null and DATEDIFF(expect_finish_at,finish_at) < 0))');
            } else {
                $ticketModel->whereRaw('((finish_at is null and DATEDIFF(expect_finish_at,CURRENT_DATE()) >= 0)
                or (finish_at is not null and DATEDIFF(expect_finish_at,finish_at) >= 0))');
            }
        }
        $ticketModel->orderBy('ticket_id', 'desc');
        $paginate = $ticketModel->paginate($params['per_page'] ?? 10, ['*'], '', $params['current_page'] ?? 1);
        foreach ($paginate as $ticket) {
            $ticket->overdue_status = $ticket->getOverdueStatus();
            $ticket->permission = $this->opPermissionService->getTicketPermissions($ticket);
        }
        return [
            'current_page' => $paginate->currentPage(),
            'per_page' => $paginate->perPage(),
            'total' => $paginate->total(),
            'data' => $paginate->getCollection()
        ];
    }

    /**
     * @param int $ticket_id
     * @return Builder|Collection|Model
     * @throws Exception
     */
    public function getTicketDetail(int $ticket_id)
    {
        if (empty($ticket_id)) throw new Exception('ticket_id参数错误');
        $ticketObj = TicketModel::with([
            'createdUser:uid,username,avatar',
            'createdDept:dept_id,dept_name',
            'assignToUser:uid,username',
            'assignToDept:dept_id,dept_name'
        ])->find($ticket_id);
        if (!$ticketObj) throw new Exception('参数错误');
        $ticketObj->overdue_status = $ticketObj->getOverdueStatus();
        $ticketObj->permission = $this->opPermissionService->getTicketPermissions($ticketObj);
        return $ticketObj;
    }

    /**
     * @param array $params
     * @throws Exception
     */
    public function saveTicket(array $params)
    {
        if (isset($params['ticket_id'])) {
            $opType = OpLogService::OP_EDIT;
            $ticketObj = TicketModel::find($params['ticket_id']);
            if (!$ticketObj) throw new Exception('参数错误');
            if ($ticketObj->status != TicketModel::T_NEW) throw new Exception('该工单状态不允许编辑');
            if (!$this->opPermissionService->checkOpPermission($ticketObj, OpLogService::OP_EDIT)) {
                throw new Exception('抱歉, 你没有编辑该工单的权限');
            }
            TicketModel::where('ticket_id', $params['ticket_id'])->update($params);
            $ticketObj->refresh();
        } else {
            $opType = OpLogService::OP_CREATE;
            $userObj = app()->get(Jwt::NOW_LOGIN_USER);
            $params['created_by'] = $userObj->uid;
            $params['created_by_dept'] = $userObj->getDeptByLevel()->dept_id;
            $ticketObj = TicketModel::create($params);
        }
        $this->opLogService->writeLog($ticketObj->ticket_id, $opType);
    }

    /**
     * @param int $ticket_id
     * @throws Exception
     */
    public function cancelTicket(int $ticket_id)
    {
        if (empty($ticket_id)) throw new Exception('ticket_id参数错误');
        $ticketObj = TicketModel::find($ticket_id);
        if (!$ticketObj) throw new Exception('参数错误');
        if (in_array($ticketObj->status, [TicketModel::T_CANCELLED, TicketModel::T_COMPLETED])) {
            throw new Exception('该工单状态不允许取消');
        }
        if (!$this->opPermissionService->checkOpPermission($ticketObj, OpLogService::OP_CANCEL)) {
            throw new Exception('抱歉, 你没有权限取消该工单');
        }
        $ticketObj->status = TicketModel::T_CANCELLED;
        $ticketObj->finish_at = date('Y-m-d H:i:s');
        $ticketObj->save();
        $this->opLogService->writeLog($ticket_id, OpLogService::OP_CANCEL);
        $this->msgService->sendMsg($ticket_id, MsgService::MSG_CANCEL);
    }

    /**
     * 指派 / 再指派
     * @param array $params
     * @throws Exception
     */
    public function assignTicket(array $params)
    {
        $ticketObj = TicketModel::find($params['ticket_id']);
        if (!$ticketObj) throw new Exception('参数错误');
        if ($ticketObj->status != TicketModel::T_NEW && $ticketObj->status != TicketModel::T_WIP) {
            throw new Exception('工单当前状态不允许指派');
        }
        if ($ticketObj->assign_to == $params['assign_to'] && $ticketObj->assign_to_dept == $params['assign_to_dept']) {
            throw new Exception('无效操作');
        }
        $opType = $ticketObj->status == TicketModel::T_NEW ? OpLogService::OP_NEW_ASSIGN : OpLogService::OP_RE_ASSIGN;
        if (!$this->opPermissionService->checkOpPermission($ticketObj, $opType)) {
            throw new Exception('抱歉, 你没权限指派该任务');
        }
        $ticketData = [
            'assign_to_dept' => $params['assign_to_dept'],
            'assign_to' => $params['assign_to'],
            'status' => TicketModel::T_WIP
        ];
        if ($ticketObj->created_by != $params['assign_to'] || $ticketObj->created_by_dept != $params['assign_to_dept']) {
            $ticketData['final_handler'] = $params['assign_to'];
            $ticketData['final_handler_dept'] = $params['assign_to_dept'];
        }
        TicketModel::where('ticket_id', $params['ticket_id'])->update($ticketData);
        $this->opLogService->writeLog($params['ticket_id'], $opType);
        $this->msgService->sendMsg($params['ticket_id'], MsgService::MSG_ASSIGN);
    }

    /**
     * 发起人/承接人 完成
     * @param $ticket_id
     * @throws Exception
     */
    public function completeTicket(int $ticket_id)
    {
        if (empty($ticket_id)) throw new Exception('ticket_id参数缺失');
        $ticketObj = TicketModel::find($ticket_id);
        if (!$ticketObj) throw new Exception('参数错误');
        if ($ticketObj->status != TicketModel::T_WIP) throw new Exception('工单当前状态不允许完成');
        if ($ticketObj->assign_to == $ticketObj->created_by) { //完结工单
            if (!$this->opPermissionService->checkOpPermission($ticketObj, OpLogService::OP_COMPLETE)) {
                throw new Exception('抱歉, 你没有完结该工单权限');
            }
            $ticketObj->status = TicketModel::T_COMPLETED;
            $ticketObj->finish_at = date('Y-m-d H:i:s');
            $ticketObj->save();
            $this->opLogService->writeLog($ticket_id, OpLogService::OP_COMPLETE);
        } else { //承接人完成
            if (!$this->opPermissionService->checkOpPermission($ticketObj, OpLogService::OP_COMPLETE_ASSIGN_TK)) {
                throw new Exception('抱歉, 你没有完成该工单权限');
            }
            $ticketObj->assign_to = $ticketObj->created_by;
            $ticketObj->assign_to_dept = $ticketObj->created_by_dept;
            $ticketObj->save();
            $this->opLogService->writeLog($ticket_id, OpLogService::OP_COMPLETE_ASSIGN_TK);
            $this->msgService->sendMsg($ticket_id, MsgService::MSG_COMPLETE_ASSIGN_TK);
        }
    }

    /**
     * 发起人 催办
     * @param int $ticket_id
     * @throws Exception
     */
    public function urgeTicket(int $ticket_id)
    {
        if (empty($ticket_id)) throw new Exception('ticket_id参数缺失');
        $ticketObj = TicketModel::find($ticket_id);
        if (!$ticketObj) throw new Exception('参数错误');
        if ($ticketObj->status != TicketModel::T_WIP) throw new Exception('工单当前状态不允许催办');
        if ($ticketObj->created_by == $ticketObj->assign_to && $ticketObj->created_by_dept == $ticketObj->assign_to_dept) {
            throw new Exception('无效操作');
        }
        if (!$this->opPermissionService->checkOpPermission($ticketObj, OpLogService::OP_URGE)) {
            throw new Exception('抱歉, 你没有催办该工单权限');
        }
        $this->msgService->sendMsg($ticket_id, MsgService::MSG_URGE);
    }
}
