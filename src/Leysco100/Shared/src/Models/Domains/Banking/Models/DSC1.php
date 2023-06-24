<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Banking\Models;

use App\Domains\Administration\Models\Country;
use Illuminate\Database\Eloquent\Model;

class DSC1 extends Model
{
    protected $guarded = ['id'];
    protected $table = 'd_s_c1_s';

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
