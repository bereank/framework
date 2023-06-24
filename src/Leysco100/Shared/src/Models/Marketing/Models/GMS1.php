<?php

namespace App\Domains\Marketing\Models;

use App\Domains\Administration\Models\User;
use App\Domains\Shared\Models\APDI;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GMS1 extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'g_m_s1_s';

    protected $appends = array('state');

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function getStateAttribute()
    {
        if ($this->Status == 0) {
            return "Successfull";
        }

        if ($this->Status == 1) {
            return "Does Not Exist";
        }

        if ($this->Status == 2) {
            return "Duplicate";
        }
    }
}
