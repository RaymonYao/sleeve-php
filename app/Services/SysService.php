<?php

namespace App\Services;


use App\Models\DeptModel;
use App\Models\UserModel;
use Illuminate\Database\Eloquent\Collection;

class SysService
{
    /**
     * @param int $need_user
     * @param int $level
     * @return array
     */
    public function getDeptList(int $need_user = 0, int $level = 2)
    {
        $depotList = DeptModel::where('lv', $level)->get();
        if ($need_user) {
            foreach ($depotList as $_dl) {
                $_dl->dept_users = [];
                $deptUsers = DeptModel::with('deptUsers')
                    ->where('l_num', '>=', $_dl['l_num'])
                    ->where('r_num', '<=', $_dl['r_num'])
                    ->get();
                foreach ($deptUsers as $deptUser) {
                    $_dl->dept_users = array_merge($_dl->dept_users, $deptUser->deptUsers->toArray());
                }
            }
        }
        return $depotList;
    }

    /**
     * @return UserModel[]|Collection
     */
    public function getUserList()
    {
        return UserModel::all();
    }
}
