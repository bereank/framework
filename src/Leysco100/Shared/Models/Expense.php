<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Administration\Models\User;

class Expense extends Model
{
    protected $guarded = ['id'];
    protected $table = 'expenses';

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
}
