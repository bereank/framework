<?php
namespace Leysco100\Shared\Traits;

use App\Domains\Administration\Models\TaxGroup;



trait TaxGroupTrait
{

    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
}
