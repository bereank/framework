<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Leysco100\Shared\Models\RoutePlanning;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OTER extends Model
{


    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'o_t_e_r_s';


    
    public function children()
    {
        return $this->hasMany(OTER::class, 'parent');
    }

    public function routes()
    {
        return $this->hasMany(RoutePlanning::class, 'territory_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function parent()
    {
        return $this->belongsTo(OTER::class, 'parent');
    }

    public function parentRecursive()
    {
        return $this->parent()->with('parentRecursive');
    }

    public function getAllChildren()
    {
        $sections = new Collection();

        foreach ($this->children as $section) {
            $sections->push($section);
            $sections = $sections->merge($section->getAllChildren());
        }

        return $sections;
    }
}
