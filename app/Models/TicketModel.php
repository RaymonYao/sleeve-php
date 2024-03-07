<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class TicketModel extends Model
{
    const T_NEW = 1; //待处理
    const T_WIP = 2; //处理中
    const T_COMPLETED = 3; //已完成
    const T_CANCELLED = 4; //已取消

    const O_OVERDUE = 1; //已逾期
    const O_NOT_OVERDUE = 2; //未逾期
    const O_ABOUT_TO_OVERDUE = 3; //将逾期

    const ABOUT_TO_OVERDUE_DAYS = 2; //将逾期判断条件, 期望完成时间 - 当前时间 <= 此处定义的值 且 > 0 为将逾期

    protected $table = 'd_tickets';

    protected $primaryKey = 'ticket_id';

    protected $guarded = ['ticket_id'];

    public $timestamps = null;

    /**
     * @return string|string[]
     */
    public function getTicketStatus()
    {
        $status = [
            static::T_NEW => '待处理',
            static::T_WIP => '处理中',
            static::T_COMPLETED => '已完成',
            static::T_CANCELLED => '已取消'
        ];
        return $status[$this->status] ?? $status;
    }

    /**
     * 创建部门
     * @return HasOne
     */
    public function createdDept()
    {
        return $this->hasOne(DeptModel::class, 'dept_id', 'created_by_dept');
    }

    /**
     * 创建人
     * @return HasOne
     */
    public function createdUser()
    {
        return $this->hasOne(UserModel::class, 'uid', 'created_by');
    }

    /**
     * 创建部门的领导
     * @return HasOneThrough
     */
    public function createdDeptManager()
    {
        return $this->hasOneThrough(UserModel::class, UserDeptModel::class, 'dept_id', 'uid', 'created_by_dept', 'uid');
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

    /**
     * 被指派部门的领导
     * @return HasOneThrough
     */
    public function assignToDeptManager()
    {
        return $this->hasOneThrough(UserModel::class, UserDeptModel::class, 'dept_id', 'uid', 'assign_to_dept', 'uid');
    }

    /**
     * 最后处理人部门
     * @return HasOne
     */
    public function finalHandlerDept()
    {
        return $this->hasOne(DeptModel::class, 'dept_id', 'final_handler_dept');
    }

    /**
     * 最后处理人
     * @return HasOne
     */
    public function finalHandlerUser()
    {
        return $this->hasOne(UserModel::class, 'uid', 'final_handler');
    }

    /**
     * 最后处理人部门的领导
     * @return HasOneThrough
     */
    public function finalHandlerDeptManager()
    {
        return $this->hasOneThrough(UserModel::class, UserDeptModel::class, 'dept_id', 'uid', 'final_handler_dept', 'uid');
    }

    /**
     * 工单回复
     * @return HasMany
     */
    public function ticketPosts()
    {
        return $this->hasMany(PostModel::class, 'ticket_id', 'ticket_id');
    }

    /**
     * 查询逾期状态
     * @return int
     */
    public function getOverdueStatus()
    {
        $diffTime = in_array($this->status, [static::T_NEW, static::T_WIP]) ? time() : strtotime($this->finish_at);
        $dateDiffObj = date_diff(date_create($this->expect_finish_at), date_create(date('Y-m-d', $diffTime)));
        $diffDays = $dateDiffObj->invert ? $dateDiffObj->days : -($dateDiffObj->days);
        if (in_array($this->status, [static::T_NEW, static::T_WIP])) {
            if ($diffDays > TicketModel::ABOUT_TO_OVERDUE_DAYS) {
                $overdueStatus = static::O_NOT_OVERDUE;
            } elseif ($diffDays >= 0 && $diffDays <= TicketModel::ABOUT_TO_OVERDUE_DAYS) {
                $overdueStatus = static::O_ABOUT_TO_OVERDUE;
            } else {
                $overdueStatus = static::O_OVERDUE;
            }
        } else {
            if ($diffDays >= 0) {
                $overdueStatus = static::O_NOT_OVERDUE;
            } else {
                $overdueStatus = static::O_OVERDUE;
            }
        }
        return $overdueStatus;
    }
}
