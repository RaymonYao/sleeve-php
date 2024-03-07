<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TicketLogModel extends Model
{
    protected $table = 'd_ticket_log';

    protected $primaryKey = 'log_id';

    protected $guarded = ['log_id'];

    public $timestamps = null;

    /**
     * 操作人
     * @return HasOne
     */
    public function opUser()
    {
        return $this->hasOne(UserModel::class, 'uid', 'op_by');
    }

    /**
     * 被指派部门
     * @return HasOne
     */
    public function assignToDept()
    {
        return $this->hasOne(DeptModel::class, 'dept_id', 'assign_to_dept');
    }

    /**
     * 被指派人
     * @return HasOne
     */
    public function assignToUser()
    {
        return $this->hasOne(UserModel::class, 'uid', 'assign_to');
    }
}
