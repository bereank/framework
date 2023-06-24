<?php

namespace Leysco100\Shared\Models\Marketing\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Leysco100\Shared\Models\InventoryAndProduction\Models\OLCT;


class GPMGate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'gates';

    public function location(): BelongsTo
    {
        return $this->belongsTo(OLCT::class, 'location_id');
    }
}
