<?php

namespace App\Models;

use App\Models\TargetSetup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco\LS100SharedPackage\Models\Domains\Marketing\Models\OINV;
use Leysco\LS100SharedPackage\Models\Domains\Administration\Models\OSLP;

class TargetSalesEmp extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'target_sales_emps';

    public function setup()
    {

        return $this->belongsTo(TargetSetup::class, 'target_setup_id', 'id');
    }

    public function employees()
    {

        return $this->belongsTo(OSLP::class, 'SlpCode', 'SlpCode');
    }

    public function invoices()
    {
        return $this->hasMany(OINV::class, 'SlpCode', 'SlpCode');
    }
}
