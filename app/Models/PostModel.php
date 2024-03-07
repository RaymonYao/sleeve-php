<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PostModel extends Model
{
    protected $table = 'd_posts';

    protected $primaryKey = 'post_id';

    protected $guarded = [];

    public $timestamps = null;

    /**
     * @return BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(TicketModel::class, 'ticket_id', 'ticket_id');
    }

    /**
     * @return HasOne
     */
    public function createdUser()
    {
        return $this->hasOne(UserModel::class, 'uid', 'created_by');
    }
}
