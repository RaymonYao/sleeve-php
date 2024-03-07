<?php

namespace App\Services;

use App\Http\Middleware\Jwt;
use App\Models\PostModel;
use App\Models\TicketModel;
use Exception;

class PostService
{
    /**
     * @var OpLogService
     */
    protected $opLogService;

    /**
     * @var MsgService
     */
    protected $msgService;

    public function __construct(OpLogService $opLogService, MsgService $msgService)
    {
        $this->opLogService = $opLogService;
        $this->msgService = $msgService;
    }

    /**
     * @param int $ticket_id
     * @return mixed
     * @throws Exception
     */
    public function getPostList(int $ticket_id)
    {
        if (empty($ticket_id)) throw new Exception('ticket_id参数错误');
        $posts = PostModel::where('ticket_id', $ticket_id)->get();
        foreach ($posts as $post) {
            if ($post->createdUser) $post->createdUser->dept_name = $post->createdUser->getDeptByLevel()->dept_name;
        }
        return $posts;
    }

    /**
     * 回复(工单/回复)
     * @param $params
     * @throws Exception
     */
    public function reply($params)
    {
        $ticketObj = TicketModel::find($params['ticket_id']);
        if (!$ticketObj) throw new Exception('参数错误');
        if (!in_array($ticketObj->status, [TicketModel::T_NEW, TicketModel::T_WIP])) throw new Exception('工单当前状态不允许回复');
        $userObj = app()->get(Jwt::NOW_LOGIN_USER);
        $params['created_by'] = $userObj->uid;
        PostModel::create($params);
        $postObj = null;
        if (isset($params['parent_id'])) {
            $postObj = PostModel::find($params['parent_id']);
            if (!$postObj) throw new Exception('参数错误');
        }
        $this->opLogService->writeLog($ticketObj->ticket_id, OpLogService::OP_REPLY);
        $this->msgService->sendMsg($ticketObj->ticket_id, MsgService::MSG_REPLY, $postObj ? $postObj->created_by : 0);
    }
}
