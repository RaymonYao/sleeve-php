<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class UserModel extends Model
{
    protected $table = 'sys_users';

    protected $primaryKey = '_id';

    protected $guarded = [];

    public $timestamps = null;

    /**
     * @return HasManyThrough
     */
    public function userDepts()
    {
        return $this->hasManyThrough(DeptModel::class, UserDeptModel::class, 'uid', 'dept_id', 'uid', 'dept_id');
    }

    /**
     * 根据用户获取所在的二级部门
     * @param int $level
     * @return mixed
     */
    public function getDeptByLevel($level = 2)
    {
        $selector = DeptModel::where('lv', $level)->orderBy('super_admin', 'desc')->where(function ($query) {
            foreach (UserDeptModel::where('uid', $this->uid)->get() as $_ud) {
                $query->orWhere([['l_num', '<=', $_ud->dept->l_num], ['r_num', '>=', $_ud->dept->r_num]]);
            }
        });
        return $selector->first();
    }

    /**
     * 是否有超级管理员角色(需往上层寻找)
     * @return bool
     */
    public function isSuperAdmin()
    {
        $isAdmin = false;
        foreach ($this->userDepts as $uDept) {
            $count = DeptModel::where('l_num', '<=', $uDept->l_num)
                ->where('r_num', '>=', $uDept->r_num)
                ->where('super_admin', DeptModel::SUPER_ADMIN)
                ->count();
            if ($count > 0) {
                $isAdmin = true;
                break;
            }
        }
        return $isAdmin;
    }
}
