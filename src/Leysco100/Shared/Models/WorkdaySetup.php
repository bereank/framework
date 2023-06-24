<?php

namespace App\Models;

use App\Models\GpsSetup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkdaySetup extends Model
{
    use HasFactory;

    public function gps()
    {
        return $this->belongsTo(GpsSetup::class, 'gps_setup_id');
    }
}
