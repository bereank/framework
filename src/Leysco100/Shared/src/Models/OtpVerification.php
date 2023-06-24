<?php

namespace Leysco\GatePassManagementModule\Models;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class OtpVerification extends Model
{
    use HasFactory;
    protected $table = 'otp_verification';
    protected $guarded = [];

    public function creator()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'UserSign', 'id');
    }
}
