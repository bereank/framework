<?php

namespace Leysco100\Shared\Models\FormSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class FI100 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'f_i100_s';
}
