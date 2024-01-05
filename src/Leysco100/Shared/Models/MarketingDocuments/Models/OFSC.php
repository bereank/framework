<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OFSC extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $table = "o_f_s_c_s";

}
