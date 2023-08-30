<?php

namespace Leysco100\Shared\Models;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;


class OtpVerification extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $table = 'otp_verification';
    protected $guarded = [];

    public function creator()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'UserSign', 'id');
    }
}
