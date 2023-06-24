<?php

namespace App\Domains\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EODC extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'e_o_d_c_s';

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }
}
