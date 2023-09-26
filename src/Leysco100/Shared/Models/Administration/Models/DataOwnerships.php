<?php


namespace Leysco100\Shared\Models\Administration\Models;

use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class DataOwnerships extends Model
{
    use HasFactory, UsesTenantConnection;
    //numbering series
    protected $guarded = ['id'];
    protected $table = 'data_ownerships';


    public function document()
    {
        return $this->belongsTo(APDI::class, 'ObjType', 'ObjectId');
    }
}
