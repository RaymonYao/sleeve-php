<?php

/**
 * @see https://ding-doc.dingtalk.com/doc#/
 */

namespace App\Services;

use App\Models\UserModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DingTalkService
{
    /**
     * @param $code
     * @param int $env //与前端约定好的环境类型, 正式环境为0, 其他为1
     * @return array|mixed
     * @throws \Exception
     */
    public function loginDingTalk($code, $env = 0)
    {
        if ($env == 1) {
            $uInfo['userid'] = $code;//测试
        } else {
            $userInfoUrl = 'https://oapi.dingtalk.com/user/getuserinfo?access_token=' . $this->getAccessToken() . '&code=' . $code;
            $uInfo = Http::get($userInfoUrl)->json();   //用户简要信息
            if ($uInfo['errcode'] != 0) throw new \Exception('登录失败');
        }
        $uObj = UserModel::where('uid', $uInfo['userid'])->first();
        if (!$uObj) throw new \Exception('验证成功, 登录失败, 请联系管理员更新用户列表!');
        return $uObj->toArray();
    }

    /**
     * @see https://ding-doc.dingtalk.com/doc#/serverapi2/ye8tup/c624d91c
     * @param string $uid
     * @param string $msg
     * @param string $url
     */
    public function sendDingMsg($uid, $msg, $url)
    {
        $sendUrl = 'https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2';
        $client = new \DingTalkClient(\DingTalkConstant::$CALL_TYPE_OAPI, \DingTalkConstant::$METHOD_POST, \DingTalkConstant::$FORMAT_JSON);
        $oapiMsgCorp = new \OapiMessageCorpconversationAsyncsendV2Request();
        $oapiMsgCorp->setAgentId(config('dingtalk.agent_id'));
        $oapiMsgCorp->setToAllUser(false);
        $oapiMsgCorp->setUseridList($uid);
        $oapiMsgCorp->setMsg([
            'msgtype' => 'action_card',//默认为卡片模式, 以后需要其他类型再重新封装
            'action_card' => [
                'title' => '蜂鸟工单',
                'markdown' => $msg,
                'single_title' => '查看工单',
                'single_url' => $url
            ]
        ]);
        $res = $client->execute($oapiMsgCorp, $this->getAccessToken(), $sendUrl);
        if ($res->errcode != 0) {
            Log::notice('发送钉钉消息失败, Message: ' . json_encode(['to' => $uid, 'msg' => $msg, 'url' => $url]));
        }
    }

    /**
     * 获取部门
     * @return array
     * @throws \Exception
     */
    public function getDeptList()
    {
        $deptUri = 'https://oapi.dingtalk.com/department/list?access_token=' . $this->getAccessToken();
        $deptList = Http::get($deptUri)->json();
        if ($deptList['errcode'] != 0) {
            throw new \Exception('获取部门失败');
        }
        return $deptList['department'];
    }

    /**
     * 根据部门获取用户
     * @param $dept_id
     * @return array
     * @throws \Exception
     */
    public function getUserByDept($dept_id)
    {
        $pageSize = 100;
        $params = [
            'access_token' => $this->getAccessToken(),
            'department_id' => $dept_id,
            'offset' => 0,
            'size' => $pageSize
        ];
        $result = [];
        while (true) {
            $deptUserListUri = 'https://oapi.dingtalk.com/user/listbypage?' . http_build_query($params);
            $deptUserList = Http::get($deptUserListUri)->json();
            $result = array_merge($result, $deptUserList['userlist'] ?? []);
            if ($deptUserList['errcode'] != 0) throw new \Exception('获取部门用户失败');
            if (!$deptUserList['hasMore']) break;
            $params['offset'] += $pageSize;
        }
        return $result;
    }

    /**
     * @param string $key
     * @return string|null
     * @throws \Exception
     */
    private function getAccessToken($key = 'access_token')
    {
        $tokenKey = 'ding_talk_token';
        $tokenData = Cache::get($tokenKey);
        if (!$tokenData) {
            $accessUri = 'https://oapi.dingtalk.com/gettoken?appkey=' . config('dingtalk.app_key') . '&appsecret=' . config('dingtalk.app_secret');
            $tokenData = Http::get($accessUri)->json();
            if ($tokenData['errcode'] != 0) {
                throw new \Exception('获取token 失败, ' . ($tokenData['errmsg'] ?? '未知错误!'));
            }
            Cache::put($tokenKey, $tokenData, $tokenData['expires_in']);
        }
        return $tokenData[$key] ?? null;
    }
}
