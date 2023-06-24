<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class OSLT extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_s_l_t_s';

    public function creator()
    {
        return $this->belongsTo(User::class, 'Owner');
    }
}
