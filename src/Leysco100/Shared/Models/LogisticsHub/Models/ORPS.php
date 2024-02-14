<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\LogisticsHub\Models\OCLG;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\Administration\Models\OTER;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCRD;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ORPS extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    public function owner()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function rows()
    {
        return $this->hasMany(CRD16::class, 'RouteID');
    }

    public function outlets()
    {
       // return $this->hasMany(CRD16::class,  'RouteID')->withPivot('bpartner');
          return $this->belongsToMany(OCRD::class, CRD16::class, 'RouteID', 'CardCode');
     
    }

    public function calls()
    {
        return $this->hasMany(OCLG::class, 'RouteCode');
    }

    public function territory()
    {
        return $this->belongsTo(OTER::class, 'TerritoryID');
    }
}
