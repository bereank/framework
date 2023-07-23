<?php
namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class MobileNavBar extends Model
{
    use HasFactory,UsesTenantConnection;
    protected $table = "mobile_nav_bars";
    protected $guarded = [];
}
