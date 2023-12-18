<?php

namespace Leysco100\Shared\Models\InventoryAndProduction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Leysco100\Shared\Models\Administration\Models\User;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OEDG extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $guarded = ['id'];
    protected $appends = ['type','obj_type'];
    public function edg1()
    {
        return $this->hasMany(EDG1::class, 'DocEntry');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'UserSign');
    }

    public function getTypeAttribute()
    {
        switch ($this->attributes['Type']) {
            case 'A':
                return "All BPs";
            case 'C':
                return "Customer Group";
            case 'S':
                return "Specific BP";
            case 'V':
                    return "Vendor Group";
            default:
                return "Unknown";
        }
    }
        public function getObjTypeAttribute()
        {
            switch ($this->attributes['ObjType']) {
                case -1:
                    return "-1";
                case 10 :
                    return "Card Payment Groups";
                case 1 :
                    return "Cards";
                
                default:
                    return "Unknown";
            }
    }

     
}
