<?php
namespace Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models;

use App\Domains\BusinessPartner\Models\OCRD;
use App\Domains\InventoryAndProduction\Models\IGN1;
use App\Domains\Marketing\Models\RDR1;
use App\Domains\Shared\Models\APDI;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class OIGN extends Model
{
    protected $guarded = ['id'];
    protected $table = 'o_i_g_n_s';

    public function outlet()
    {
        return $this->belongsTo(OCRD::class, 'CardCode');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function items()
    {
        return $this->hasMany(RDR1::class, 'DocEntry');
    }

    public function ign1()
    {
        return $this->hasMany(IGN1::class, 'DocEntry');
    }

    public function rows()
    {
        return $this->hasMany(IGN1::class, 'DocEntry');
    }

    public function objecttype()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectID');
    }
}
