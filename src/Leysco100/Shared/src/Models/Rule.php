<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $guarded = ['id'];
    protected $table = 'rules';

    public function surveys()
    {
        return $this->belongsTo('App\Survey', 'survey_id');
    }

    public function options()
    {
        return $this->hasMany('App\RulesChoices', 'rules_id');
    }
}
