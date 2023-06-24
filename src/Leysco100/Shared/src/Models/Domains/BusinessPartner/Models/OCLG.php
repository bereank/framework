<?php
namespace Leysco\LS100SharedPackage\Models\Domains\BusinessPartner\Models;

use App\Models\CallObjective;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use App\Domains\Administration\Models\OSLP;

class OCLG extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_c_l_g_s';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }

    public function employees()
    {
        return $this->belongsTo(OSLP::class, 'SlpCode');
    }

    public function objectives()
    {
        return $this->hasMany(CallObjective::class, 'CallCode');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }
}
