<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeModel extends Model
{
    protected $table = 'theme';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = null;
}
