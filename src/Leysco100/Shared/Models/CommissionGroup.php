<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionGroup extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $table = 'commission_groups';
}
