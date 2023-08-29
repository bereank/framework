<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\Administration\Models\OSLP;
use Leysco100\Shared\Models\MarketingDocuments\Models\OINV;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class TargetSalesEmp extends Model
{
    use HasFactory, UsesTenantConnection;

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
