<?php

namespace App\Domains\Administration\Models;

use App\Domains\BusinessPartner\Models\OBPL;
use App\Domains\FormSetting\Models\FM100;
use App\Domains\HumanResourse\Models\OHEM;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;

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

    /**
     * Override the mail body for reset password notification mail.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\MailResetPasswordNotification($token));
    }
}
