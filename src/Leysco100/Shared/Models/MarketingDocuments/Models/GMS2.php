<?php

namespace Leysco100\Shared\Models\MarketingDocuments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GMS2 extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'g_m_s2_s';
}
