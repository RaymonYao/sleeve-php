<?php


namespace App\Console\Commands;


use App\Models\DeptModel;
use App\Models\UserModel;
use App\Models\UserDeptModel;
use App\Services\DingTalkService;
use Illuminate\Console\Command;

class UpdateUser extends Command
{
    protected $signature = 'command:updateUser';

    protected $description = '更新用户';

    /**
     * @var DingTalkService
     */
    protected $ddService;

    public function __construct(DingTalkService $ddService)
    {
        parent::__construct();
        $this->ddService = $ddService;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $deptIds = array_column(DeptModel::all(['dept_id'])->toArray(), 'dept_id');
        $uidDepts = [];
        foreach ($deptIds as $deptId) {
            foreach ($this->ddService->getUserByDept($deptId) as $uInfo) {
                $userObj = UserModel::updateOrCreate(['uid' => $uInfo['userid']], [
                    'uid' => $uInfo['userid'],
                    'unionid' => $uInfo['unionid'],
                    'username' => $uInfo['name'] ?? '',
                    'is_admin' => $uInfo['isAdmin'] ? DeptModel::SUPER_ADMIN : 0,
                    'position' => $uInfo['position'] ?? '',
                    'jobnumber' => $uInfo['jobnumber'] ?? '',
                    'avatar' => $uInfo['avatar'] ?? '',
                ]);
                $uidDepts[(string)$userObj->uid][] = $deptId;
                UserDeptModel::updateOrCreate([
                    'uid' => $userObj->uid,
                    'dept_id' => $deptId,
                ], [
                    'uid' => $userObj->uid,
                    'dept_id' => $deptId,
                    'is_leader' => $uInfo['isLeader']
                ]);
            }
        }
        $uids = array_keys($uidDepts);
        array_walk($uids, function (&$v, $_) {
            $v = (string)$v;
        });
        UserModel::whereNotIn('uid', $uids)->delete();
        UserDeptModel::whereNotIn('uid', $uids)->delete();
        foreach ($uidDepts as $_uid => $deptIds) {
            UserDeptModel::where('uid', (string)$_uid)->whereNotIn('dept_id', $deptIds)->delete();
        }
        echo 'success', PHP_EOL;
    }
}
