<?php

namespace App\Models;


use App\Models\Targets;
use App\Models\TargetItems;
use App\Models\TargetSalesEmp;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\OINV;



class TargetSetup extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'target_setups';

    public function rows()
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

        return $this->belongsTo('App\Domains\InventoryAndProduction\Models\OUOM',  'UoM');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userSign');
    }
}
