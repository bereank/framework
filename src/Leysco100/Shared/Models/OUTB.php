<?php

namespace Leysco100\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OUTB extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $table = 'o_u_t_b';
    protected $guarded = [];

    protected $appends = array('object');

    public function getObjectAttribute()
    {
        if ($this->ObjectType == 0) {
            return "No Object";
        }

        if ($this->ObjectType == 1) {
            return "Master Data";
        }

        if ($this->ObjectType == 2) {
            return "Master Data Rows";
        }
        if ($this->ObjectType == 3) {
            return "Document";
        }

        if ($this->ObjectType == 4) {
            return "Document Rows";
        }
    }
}
