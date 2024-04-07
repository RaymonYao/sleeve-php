<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpuModel extends Model
{
    protected $table = 'spu';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = null;
}
