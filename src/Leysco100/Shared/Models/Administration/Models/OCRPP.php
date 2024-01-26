<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OCRPP extends Model
{
    use UsesTenantConnection, HasFactory;
    protected $guarded = ['id'];
    protected $table = 'o_c_r_p_p_s';

    public function creditCard()

    {
        return $this->hasMany(OTER::class, 'CreditCard', 'CreditCard');
    }
}
