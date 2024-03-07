<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class DeptModel extends Model
{
    const SUPER_ADMIN = 1;

    protected $table = 'sys_depts';

    protected $primaryKey = 'dept_id';

    protected $guarded = [];

    public $timestamps = null;

    /**
     * @return HasManyThrough
     */
    public function deptUsers()
    {
        return $this->hasManyThrough(UserModel::class, UserDeptModel::class, 'dept_id', 'uid', 'dept_id', 'uid');
    }
}
