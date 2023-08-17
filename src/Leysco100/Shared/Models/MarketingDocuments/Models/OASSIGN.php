<?php

namespace App\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Marketing\Models\ASSIGN1;
use App\Domains\BusinessPartner\Models\OCRD;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OASSIGN extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'o_a_s_s_i_g_n_s';

    public function rows()
    {
        return $this->hasMany(ASSIGN1::class, 'DocEntry');
    }

    public function ocrd()
    {
        return $this->belongsTo(OCRD::class, 'CardCode', 'CardCode');
    }
}
