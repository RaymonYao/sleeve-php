<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDeptModel extends Model
{
    protected $table = 'sys_user_dept';

    public $timestamps = null;

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function dept()
    {
        return $this->belongsTo(DeptModel::class, 'dept_id', 'dept_id');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'uid', 'uid');
    }
}
