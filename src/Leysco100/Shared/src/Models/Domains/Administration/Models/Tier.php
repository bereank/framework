<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Administration\Models;

use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    protected $guarded = ['id'];
    protected $table = 'tiers';

    public function outlets()
    {
        return $this->hasMany(OCRD::class, 'TierCode');
    }

    public function employees()
    {
        return $this->hasMany(OSLP::class, 'TierCode');
    }
}
