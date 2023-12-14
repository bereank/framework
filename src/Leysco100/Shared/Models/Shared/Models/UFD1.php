<?php

namespace Leysco100\Shared\Models\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\CUFD;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class UFD1 extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];

    public function userField()
    {
        return $this->belongsTo(CUFD::class, 'FieldID');
    }
}
