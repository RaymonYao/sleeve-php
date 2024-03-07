<?php


namespace App\Services;


use App\Http\Middleware\Jwt;
use App\Models\TicketLogModel;
use App\Models\TicketModel;

class OpLogService
{
    const OP_CREATE = 'create';
    const OP_EDIT = 'edit';
    const OP_NEW_ASSIGN = 'new_assign';
    const OP_RE_ASSIGN = 're_assign';
    const OP_REPLY = 'reply';
    const OP_CANCEL = 'cancel';
    const OP_COMPLETE = 'complete'; //完成工单本身
    const OP_COMPLETE_ASSIGN_TK = 'complete_assign_tk'; //完成自己的操作
    const OP_URGE = 'urge'; //催办

    /**
     * @param $ticket_id
     * @param $op_type
     */
    public function writeLog($ticket_id, $op_type)
    {
        $opUser = app()->get(Jwt::NOW_LOGIN_USER);  //当前操作人
        $ticketObj = TicketModel::find($ticket_id);
        $data = [
            'ticket_id' => $ticket_id,
            'op_type' => $op_type,
            'op_by' => $opUser->uid,
        ];
        switch ($op_type) {
            case static::OP_CREATE:
                $data['description'] = '发起了工单';
                $data['result'] = json_encode([
                    'title' => $ticketObj->title,
                    'content' => $ticketObj->content,
                    'expect_finish_at' => $ticketObj->expect_finish_at,
                ]);
                break;
            case static::OP_EDIT:
                $data['description'] = '编辑了工单';
                $data['result'] = json_encode([
                    'title' => $ticketObj->title,
                    'content' => $ticketObj->content,
                    'expect_finish_at' => $ticketObj->expect_finish_at,
                ]);
                break;
            case static::OP_NEW_ASSIGN:
                $data['description'] = '指派了工单';
                $data['assign_to'] = $ticketObj->assign_to;
                $data['assign_to_dept'] = $ticketObj->assign_to_dept;
                break;
            case static::OP_RE_ASSIGN:
                $data['description'] = '重新指派了工单';
                $data['assign_to'] = $ticketObj->assign_to;
                $data['assign_to_dept'] = $ticketObj->assign_to_dept;
                break;
            case static::OP_REPLY:
                $data['description'] = '回复了工单';
                break;
            case static::OP_COMPLETE:
                $data['description'] = '完成了工单';
                break;
            case static::OP_COMPLETE_ASSIGN_TK:
                $data['description'] = '完成了被指派的工单';
                break;
            case static::OP_CANCEL:
                $data['description'] = '取消了工单';
                break;
        }
        TicketLogModel::create($data);
    }

    /**
     * @param int $ticket_id
     * @return mixed
     */
    public function getLog(int $ticket_id)
    {
        return TicketLogModel::select([
            'log_id', 'ticket_id', 'op_type', 'op_by', 'assign_to_dept',
            'assign_to', 'description', 'created_at'
        ])->with([
            'opUser:uid,username', 'assignToDept:dept_id,dept_name', 'assignToUser:uid,username'
        ])->where('ticket_id', $ticket_id)->get();
    }
}
