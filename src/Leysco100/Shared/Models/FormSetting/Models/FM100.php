<?php

namespace Leysco100\Shared\Models\FormSetting\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class FM100 extends Model
{
    use UsesTenantConnection;
    protected $guarded = ['id'];
    protected $table = 'f_m100_s';

    public function children()
    {
        return $this->hasMany(FM100::class, 'ParentID');
    }

    public function GrandChildren()
    {
        return $this->children()->with('GrandChildren');
    }

    public function parent()
    {
        return $this->belongsTo(FM100::class, 'ParentID');
    }

    public function parentRecursive()
    {
        return $this->parent()->with('parentRecursive');
    }

    public function usermenu()
    {
        return $this->belongsToMany(User::class, 's_e_r1_s', 'MenuID', 'UserSign');
    }
}
