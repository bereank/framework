<?php

namespace App\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Model;

class TaxGroup extends Model
{
    protected $guarded = ['id'];
    protected $table = 'tax_groups';
}
