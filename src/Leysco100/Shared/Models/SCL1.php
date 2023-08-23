<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class SCL1 extends Model
{
    use UsesTenantConnection;

    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 's_c_l1_s';

    /**
     * Mapping to Solution Table
     */
    public function oslt()
    {
        return $this->belongsTo(OSLT::class, 'solutionID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'userSign');
    }
}
