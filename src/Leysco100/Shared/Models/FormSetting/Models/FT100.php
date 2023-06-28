<?php

namespace Leysco100\Shared\Models\FormSetting\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class FT100 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'f_t100_s';


    public function tablerows()
    {
        return $this->hasMany(FTR100::class, 'TabID');
    }
}
