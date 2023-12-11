<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OUOM;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;


class TargetSetup extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = ['id'];
    protected $table = 'target_setups';

    public function document_lines()
    {
        return $this->hasMany(Targets::class, 'target_setup_id');
    }
    public function salesEmployees()
    {
        return $this->hasMany(TargetSalesEmp::class, 'target_setup_id');
    }

    public function items()
    {
        return $this->hasMany(TargetItems::class, 'target_setup_id');
    }

    public function metrics()
    {

        return $this->belongsTo(OUOM::class,  'UoM');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userSign');
    }
}