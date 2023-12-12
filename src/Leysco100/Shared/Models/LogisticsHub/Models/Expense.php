<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;


use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\User;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Expense extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'expenses';

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
}
