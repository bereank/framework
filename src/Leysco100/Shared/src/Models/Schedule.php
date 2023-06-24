<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $guarded = ['id'];
    protected $table = 'schedule';

    public function outlets()
    {
        return $this->belongsTo('App\OCRD', 'CardCode');
    }

    public function countries()
    {
        return $this->belongsTo('App\Country', 'countries_id');
    }

    public function surveys()
    {
        return $this->belongsTo('App\Survey', 'survey_id');
    }
}
