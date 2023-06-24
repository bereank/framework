<?php
namespace Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;

class OCQG extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_c_q_g_s';

    public function cqg1()
    {
        return $this->hasMany(CQG1::class, 'GroupCode');
    }

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'SlpCode');
    }
}
