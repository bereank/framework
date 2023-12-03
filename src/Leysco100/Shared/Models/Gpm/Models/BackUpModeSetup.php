<?php

namespace Leysco100\Shared\Models\Gpm\Models;


use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class BackUpModeSetup extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'UserSign');
    }
    public function template()
    {
        return $this->hasOne(FormFieldsTemplate::class, 'id', 'FieldsTemplate');
    }
    public function users()
    {
        return $this->hasMany(BackUpModUsers::class, 'BackupModeID', 'id');
    }
    public function gates()
    {
        return $this->hasMany(BackUpModGates::class, 'BackupModeID', 'id');
    }
    public function rows()
    {
        return $this->hasMany(BackUpModeEntries::class, 'DocEntry');
    }
}
