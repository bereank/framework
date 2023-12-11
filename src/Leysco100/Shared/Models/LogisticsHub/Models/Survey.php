<?php

namespace Leysco100\Shared\Models\LogisticsHub\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $guarded = ['id'];
    protected $table = 'surveys';

    public function rules()
    {
        return $this->hasMany('App\Rule', 'survey_id');
    }

    public function schedules()
    {
        return $this->hasMany('App\Schedule', 'survey_id');
    }

    public function outlets()
    {
        return $this->belongsTo('App\OCRD', 'App\Schedule', 'outletCat');
    }
}
