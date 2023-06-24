<?php
namespace Leysco100\Shared\Traits;



trait TaxGroupTrait
{

    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
}
