<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityModel extends Model
{
    protected $table = 'activity';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = null;
}
