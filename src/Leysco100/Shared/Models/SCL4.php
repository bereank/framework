<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Leysco\LS100SharedPackage\Models\Domains\Shared\Models\APDI;

class SCL4 extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 's_c_l4_s';

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'Object', 'ObjectID');
    }
}
