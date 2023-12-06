<?php

namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Channel extends Model
{
    protected $guarded = ['id'];
    protected $table = 'channels';

    use UsesTenantConnection;

    public function bpartner()
    {
        return $this->hasMany('App\OCRD', 'ChannCode');
    }

    public function employees()
    {
        return $this->hasMany('App\OSLP', 'ChannCode');
    }

    public function territory()
    {
        return $this->hasManyThrough('App\OTER', 'App\OCRD', 'Territory', 'id');
    }
}
