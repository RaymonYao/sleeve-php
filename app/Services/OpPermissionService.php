<?php

namespace App\Services;

use App\Http\Middleware\Jwt;
use App\Models\DeptModel;
use App\Models\TicketModel;
use App\Models\UserDeptModel;

class OpPermissionService
{
    /**
     * 获取有该单该类型操作权限的人
     * @param TicketModel $ticketObj
     * @param string $opType
     * @return array
     */
    public function getOpPermissionUserIds(TicketModel $ticketObj, string $opType)
    {
        $adminDeptObjs = DeptModel::where('super_admin', DeptModel::SUPER_ADMIN)->get();
        $adminDeptIds = [];
        $hasPermissionUserIds = [];
        foreach ($adminDeptObjs as $adminDeptObj) {
            $subDeptArr = DeptModel::where('l_num', '>=', $adminDeptObj->l_num)
                ->where('r_num', '<=', $adminDeptObj->r_num)
                ->get()->toArray();
            $adminDeptIds = array_merge($adminDeptIds, array_column($subDeptArr, 'dept_id'));
        }
        if (!empty($adminDeptIds)) {
            $adminUserArr = UserDeptModel::whereIn('dept_id', $adminDeptIds)->get()->toArray();
            $hasPermissionUserIds = array_column($adminUserArr, 'uid');
        }

        switch ($opType) {
            case OpLogService::OP_CANCEL:               //取消
            case OpLogService::OP_COMPLETE:             //单据完结
            case OpLogService::OP_EDIT:                 //编辑
                $hasPermissionUserIds[] = $ticketObj->created_by;
                if ($ticketObj->createdDeptManager) $hasPermissionUserIds[] = $ticketObj->createdDeptManager->uid;
                break;
            case OpLogService::OP_NEW_ASSIGN:           //新指派
            case OpLogService::OP_URGE:                 //催办
                $hasPermissionUserIds[] = $ticketObj->created_by;
                break;
            case OpLogService::OP_RE_ASSIGN:            //再指派
            case OpLogService::OP_COMPLETE_ASSIGN_TK:   //完成被指派
                $hasPermissionUserIds[] = $ticketObj->assign_to;
                if ($ticketObj->assignToDeptManager) $hasPermissionUserIds[] = $ticketObj->assignToDeptManager->uid;
                break;
        }
        return array_values(array_filter(array_unique($hasPermissionUserIds)));
    }

    /**
     * @param TicketModel $ticketObj
     * @param string $opType
     */
    public function checkOpPermission(TicketModel $ticketObj, string $opType)
    {
        $opUser = app()->get(Jwt::NOW_LOGIN_USER);
        return in_array($opUser->uid, $this->getOpPermissionUserIds($ticketObj, $opType));
    }

    /**
     * @param TicketModel $ticket
     * @return array
     */
    public function getTicketPermissions(TicketModel $ticket)
    {
        switch ($ticket['status']) {
            case TicketModel::T_NEW:
                return [
                    OpLogService::OP_EDIT => $this->getOpPermissionUserIds($ticket, OpLogService::OP_EDIT),
                    OpLogService::OP_NEW_ASSIGN => $this->getOpPermissionUserIds($ticket, OpLogService::OP_NEW_ASSIGN),
                    OpLogService::OP_CANCEL => $this->getOpPermissionUserIds($ticket, OpLogService::OP_CANCEL)
                ];
            case TicketModel::T_WIP:
                $opType = $ticket->created_by == $ticket->assign_to ? OpLogService::OP_COMPLETE : OpLogService::OP_COMPLETE_ASSIGN_TK;
                return [
                    OpLogService::OP_RE_ASSIGN => $this->getOpPermissionUserIds($ticket, OpLogService::OP_RE_ASSIGN),
                    OpLogService::OP_CANCEL => $this->getOpPermissionUserIds($ticket, OpLogService::OP_CANCEL),
                    $opType => $this->getOpPermissionUserIds($ticket, $opType),
                    OpLogService::OP_URGE => $this->getOpPermissionUserIds($ticket, OpLogService::OP_URGE)
                ];
        }
        return [];
    }
}
