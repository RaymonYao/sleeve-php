<?php


namespace App\Services;


use App\Http\Middleware\Jwt;
use App\Models\TicketModel;

class MsgService
{
    const MSG_ASSIGN = 'assign';                        //指派
    const MSG_REPLY = 'reply';                          //回复
    const MSG_CANCEL = 'cancel';                        //取消
    const MSG_ABOUT_TO_OVERDUE = 'about_to_verdue';     //将逾期
    const MSG_OVERDUE = 'overdue';                      //逾期
    const MSG_COMPLETE_ASSIGN_TK = 'complete_assign_tk';//完成被指派工单
    const MSG_URGE = 'urge';                            //工单催办

    /**
     * @var DingTalkService
     */
    protected $dingTalkService;

    public function __construct(DingTalkService $talkService)
    {
        $this->dingTalkService = $talkService;
    }

    public function getMsgTitle()
    {
        return [
            static::MSG_ASSIGN => '工单已被指派给：{to_username}',
            static::MSG_REPLY => '工单有新回复，当前承接人：{to_username}',
            static::MSG_CANCEL => '工单已取消',
            static::MSG_COMPLETE_ASSIGN_TK => '工单已完成',
            static::MSG_ABOUT_TO_OVERDUE => '工单即将逾期，当前承接人：{to_username}',
            static::MSG_OVERDUE => '工单已逾期，当前承接人：{to_username}',
            static::MSG_URGE => '请及时处理工单'
        ];
    }

    public function getMsgTemplate()
    {
        return '#### <font data-type="{operation_time}" color="#3296FA" bgcolor="green">蜂鸟工单</font>
----
#### **{title}**
- <font color="#959BA1">工单编号：</font>**#{ticket_id}**
- <font color="#959BA1">工单标题：</font>**{ticket_title}**
- <font color="#959BA1">期望完成时间：</font>**{expect_finish_time}**

<font color="#67C23A">{status}</font>';
    }

    /**
     * 根据操作类型发送钉钉通知
     * @param $ticket_id
     * @param string $op_type
     * @param int $reply_to //被回复的消息的创建人
     */
    public function sendMsg($ticket_id, $op_type = MsgService::MSG_ASSIGN, $reply_to = 0)
    {
        $template = $this->getMsgTemplate();
        $title = $this->getMsgTitle();
        $msgContent = str_replace('{title}', $title[$op_type] ?? '', $template);
        $ticketObj = TicketModel::where('ticket_id', $ticket_id)->first();
        $opUser = app()->get(Jwt::NOW_LOGIN_USER);  //当前操作人
        $toDdUidArr = [];
        switch ($op_type) {
            case static::MSG_ASSIGN:    //指派: 发消息给被指派人, (如果是管理员/二级部门领导操作则同时发给发起人)
            case static::MSG_REPLY:     //回复工单: 发给非自己的其他人(发起人, 被指派人)
            case static::MSG_CANCEL:    //取消工单: 发给承接人, (如果是管理员/二级部门领导取消则还需发送给发起人)
            case static::MSG_COMPLETE_ASSIGN_TK: //完成了被指派的工单
                $checkDuplicate = [];
                if ($ticketObj->createdUser && $ticketObj->createdUser->uid != $opUser->uid) {
                    $toDdUidArr[] = $ticketObj->createdUser->uid;
                    $checkDuplicate[] = $ticketObj->createdUser->uid;
                    $checkDuplicate[] = $opUser->uid;
                }
                if ($ticketObj->assignToUser && $ticketObj->assignToUser->uid != $opUser->uid) {
                    $toDdUidArr[] = $ticketObj->assignToUser->uid;
                    $checkDuplicate[] = $ticketObj->assignToUser->uid;
                }
                if ($reply_to && !in_array($reply_to, $checkDuplicate)) { //被回复的消息创建人
                    $toDdUidArr[] = $reply_to;
                }
                if ($ticketObj->final_handler && !in_array($ticketObj->final_handler, $checkDuplicate)) { //最后处理人
                    $toDdUidArr[] = $ticketObj->final_hansdler;
                }
                break;
            case static::MSG_URGE:  //催单
                $toDdUidArr[] = $ticketObj->assignToUser->uid;
                break;
            case static::MSG_ABOUT_TO_OVERDUE:  //将逾期
            case static::MSG_OVERDUE:           //已逾期
                $checkDuplicate = [];
                if ($ticketObj->createdUser) {  //发起人可能已离职被删除
                    $checkDuplicate[] = $ticketObj->createdUser->uid;
                    $toDdUidArr[] = $ticketObj->createdUser->uid;
                }
                if ($ticketObj->assignToUser && !in_array($ticketObj->assignToUser->uid, $checkDuplicate)) {
                    $toDdUidArr[] = $ticketObj->assignToUser->uid;
                    $checkDuplicate[] = $ticketObj->assignToUser->uid;
                }
                if ($ticketObj->createdDeptManager && !in_array($ticketObj->createdDeptManager->uid, $checkDuplicate)) {
                    $toDdUidArr[] = $ticketObj->createdDeptManager->uid;
                    $checkDuplicate[] = $ticketObj->createdDeptManager->uid;
                }
                if ($ticketObj->assignToDeptManager && !in_array($ticketObj->assignToDeptManager->uid, $checkDuplicate)) {
                    $toDdUidArr[] = $ticketObj->assignToDeptManager->uid;
                }
                break;
        }
        $toDdUidArr = array_unique($toDdUidArr);
        $this->sendDingMsg($ticketObj, $msgContent, $toDdUidArr);
    }

    public function sendDingMsg($ticketObj, $msgContent, $toDdUidArr)
    {
        if (!$toDdUidArr) return;
        $templateKv = [
            'ticket_id' => $ticketObj->ticket_id,
            'ticket_title' => $ticketObj->title,
            'expect_finish_time' => $ticketObj->expect_finish_at,
            'to_username' => $ticketObj->assignToUser ? $ticketObj->assignToUser->username : null,
            'operation_time' => time(),
            'status' => $ticketObj->getTicketStatus()
        ];
        foreach ($templateKv as $k => $v) {
            $msgContent = str_replace('{' . $k . '}', $v, $msgContent);
        }
        foreach ($toDdUidArr as $toDdUid) {
            $this->dingTalkService->sendDingMsg($toDdUid, $msgContent, config('ticket.base_url') . '/#/detail?type=msg&id=' . $ticketObj->ticket_id);
        }
    }
}
