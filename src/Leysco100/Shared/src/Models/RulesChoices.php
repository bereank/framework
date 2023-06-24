<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RulesChoices extends Model
{
    protected $guarded = ['id'];
    protected $table = 'rules_choices';

    public function rules()
    {
        return $this->belongsTo('App\Rule', 'rules_id');
    }
}
