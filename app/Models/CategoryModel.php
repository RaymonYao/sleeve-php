<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    protected $table = 'category';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = null;
}
