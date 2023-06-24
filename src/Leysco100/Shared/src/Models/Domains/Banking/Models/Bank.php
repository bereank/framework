<?php
namespace Leysco\LS100SharedPackage\Models\Domains\Banking\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Administration\Models\Country;

class Bank extends Model
{
    protected $guarded = ['id'];
    protected $table = 'banks';

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
