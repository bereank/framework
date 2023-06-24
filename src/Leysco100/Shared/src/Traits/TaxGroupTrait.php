<?php
namespace Leysco100\Shared\Traits;

use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\TaxGroup;

trait TaxGroupTrait
{

    public function taxgroup()
    {
        return $this->belongsTo(TaxGroup::class, 'TaxCode', 'code');
    }
}
