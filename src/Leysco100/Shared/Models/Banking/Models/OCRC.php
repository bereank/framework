<?php

namespace Leysco100\Shared\Models\Banking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\OCRPP;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OCRC extends Model
{
    use UsesTenantConnection;

    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'o_c_r_c_s';

    public function paymentMetod()

    {
        return $this->hasMany(OCRPP::class, 'CreditCard', 'CreditCard');
    }
}
