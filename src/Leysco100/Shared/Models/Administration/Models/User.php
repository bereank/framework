<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Laravel\Sanctum\HasApiTokens;

use Leysco100\Shared\Models\BusinessPartner\Models\OBPL;
use Leysco100\Shared\Models\Shared\Models\FM100;
use Leysco100\Shared\Models\HumanResourse\Models\OHEM;
use Leysco100\Shared\Models\Gpm\Models\GPMGate;
use Leysco100\Shared\Models\MarketingDocuments\Models\ORDR;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    use UsesTenantConnection;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
        'password',
        'DfltsGroup',
        'account',
        'phone_number',
        'all_Branches',
        'SUPERUSER',
        'CompanyID',
        'Department',
        'ExtRef',
        'EmpID',
        'signaturePath',
        'type',
        'status',
        'useLocalSearch',
        'localUrl',
        'gate_id',
        'last_login_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function gates()
    {
        return $this->belongsTo(GPMGate::class, 'gate_id');
    }

    public function ordr()
    {
        return $this->hasMany(ORDR::class, 'UserSign');
    }

    public function nnm2()
    {
        return $this->hasMany(NNM2::class, 'UserSign');
    }

    public function oudg()
    {
        return $this->belongsTo(OUDG::class, 'DfltsGroup');
    }

    //allowed
    public function fm100()
    {
        return $this->belongsToMany(FM100::class, 's_e_r1_s', 'UserSign', 'MenuID');
    }

    public function branches()
    {
        return $this->belongsToMany(OBPL::class, 'u_s_r1_s', 'user_id', 'BPLId');
    }

    public function ohem()
    {
        return $this->belongsTo(OHEM::class, 'owner', 'empID');
    }


}
