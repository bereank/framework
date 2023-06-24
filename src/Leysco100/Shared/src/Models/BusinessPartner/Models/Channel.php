<?php

namespace App\Domains\BusinessPartner\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $guarded = ['id'];
    protected $table = 'channels';

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
